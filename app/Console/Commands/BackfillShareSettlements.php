<?php

namespace App\Console\Commands;

use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Services\TransactionNumberService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BackfillShareSettlements extends Command
{
    protected $signature = 'shares:backfill-settlements {--apply : Create settlements and synchronize safe shareholder positions}';

    protected $description = 'Dry-run or safely backfill historical full-withdrawal share settlements';

    public function handle(): int
    {
        $analyses = Shareholder::query()
            ->whereHas('shareTransactions', fn ($query) => $query->where('status', 'active'))
            ->orderBy('id')
            ->get()
            ->map(fn (Shareholder $shareholder) => $this->analyse($shareholder));

        $rows = $analyses->flatMap(function (array $analysis): array {
            return array_map(fn (array $candidate): array => [
                $analysis['shareholder']->id,
                $analysis['shareholder']->code,
                $analysis['shareholder']->name,
                $candidate['withdrawal']->id,
                $candidate['withdrawal']->transaction_number,
                $this->money($candidate['basis_cents']),
                $this->money($candidate['withdrawal_cents']),
                $candidate['type'] ?? 'none required',
                $this->money(abs($candidate['residual_cents'])),
                $analysis['current_kitta'],
                $this->money($analysis['current_investment_cents']),
                $analysis['safe'] ? 'YES' : 'NO: '.$analysis['reason'],
            ], $analysis['candidates']);
        })->all();

        $this->table([
            'Shareholder ID', 'Code', 'Name', 'Withdrawal ID', 'Withdrawal No.',
            'Investment Basis', 'Withdrawal Amount', 'Settlement Type',
            'Settlement Amount', 'Current Kitta', 'Current Investment', 'Safe',
        ], $rows);

        foreach ($analyses->where('safe', false) as $analysis) {
            $this->warn(sprintf(
                'SKIPPED shareholder %d (%s): %s',
                $analysis['shareholder']->id,
                $analysis['shareholder']->code,
                $analysis['reason']
            ));
        }

        $unsafeCount = $analyses->where('safe', false)->count();
        $candidateCount = $analyses->sum(fn (array $analysis) => count($analysis['candidates']));

        if (! $this->option('apply')) {
            $this->info("DRY RUN: {$candidateCount} historical full withdrawal(s); no database changes made.");
            if ($unsafeCount > 0) {
                $this->warn("{$unsafeCount} shareholder history set(s) are unsafe and will be skipped.");
            }

            return self::SUCCESS;
        }

        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($analyses, &$created, &$updated): void {
            foreach ($analyses->where('safe', true) as $analysis) {
                $shareholder = Shareholder::query()
                    ->lockForUpdate()
                    ->findOrFail($analysis['shareholder']->id);

                if (
                    (int) $shareholder->kitta !== $analysis['current_kitta']
                    || $this->toCents($shareholder->total_investment) !== $analysis['current_investment_cents']
                ) {
                    throw new RuntimeException(
                        "Shareholder {$shareholder->id} changed during backfill; the entire apply was rolled back."
                    );
                }

                foreach ($analysis['candidates'] as $candidate) {
                    if ($candidate['residual_cents'] === 0) {
                        continue;
                    }

                    $withdrawal = ShareTransaction::query()
                        ->lockForUpdate()
                        ->findOrFail($candidate['withdrawal']->id);

                    if ($withdrawal->status !== 'active') {
                        throw new RuntimeException(
                            "Withdrawal {$withdrawal->id} changed during backfill; the entire apply was rolled back."
                        );
                    }

                    $settlement = ShareTransaction::query()->firstOrCreate(
                        ['settlement_of_id' => $withdrawal->id],
                        [
                            'transaction_number' => TransactionNumberService::temporary(),
                            'transaction_type' => $candidate['type'],
                            'shareholder_id' => $withdrawal->shareholder_id,
                            'account_id' => $withdrawal->account_id,
                            'date_ad' => $withdrawal->date_ad->format('Y-m-d'),
                            'date_bs' => $withdrawal->date_bs,
                            'financial_year' => $withdrawal->financial_year,
                            'kitta' => 0,
                            'per_kitta_value' => '0.00',
                            'total_amount' => $this->money(abs($candidate['residual_cents'])),
                            'investment_effect' => $this->money(abs($candidate['residual_cents'])),
                            'reference' => $withdrawal->transaction_number,
                            'note' => 'Historical full withdrawal share settlement backfill.',
                            'status' => 'active',
                            'created_by' => $withdrawal->created_by,
                        ]
                    );

                    if ($settlement->wasRecentlyCreated) {
                        $settlement->transaction_number = TransactionNumberService::fromId('SET-', $settlement->id);
                        $settlement->save();
                        $created++;
                    }
                }

                $expectedInvestment = intdiv($analysis['corrected_investment_cents'], 100);
                if (
                    (int) $shareholder->kitta !== $analysis['calculated_kitta']
                    || (int) $shareholder->total_investment !== $expectedInvestment
                ) {
                    $shareholder->kitta = $analysis['calculated_kitta'];
                    $shareholder->total_investment = $expectedInvestment;
                    $shareholder->save();
                    $updated++;
                }
            }
        });

        $this->info("APPLIED: {$created} settlement(s) created; {$updated} shareholder position(s) synchronized.");
        if ($unsafeCount > 0) {
            $this->warn("{$unsafeCount} unsafe shareholder history set(s) were skipped.");
        }

        return self::SUCCESS;
    }

    private function analyse(Shareholder $shareholder): array
    {
        $transactions = ShareTransaction::query()
            ->where(function ($query) use ($shareholder): void {
                $query->where('shareholder_id', $shareholder->id)
                    ->orWhere('to_shareholder_id', $shareholder->id);
            })
            ->orderBy('date_ad')
            ->orderBy('id')
            ->get();

        $settlements = $transactions
            ->filter(fn (ShareTransaction $transaction) => in_array($transaction->transaction_type, [
                ShareTransaction::TYPE_SETTLEMENT_GAIN,
                ShareTransaction::TYPE_SETTLEMENT_LOSS,
            ], true))
            ->groupBy('settlement_of_id');

        $kitta = 0;
        $observedInvestment = 0;
        $correctedInvestment = 0;
        $safe = true;
        $reasons = [];
        $candidates = [];

        foreach ($transactions->where('status', 'active') as $transaction) {
            if (in_array($transaction->transaction_type, [
                ShareTransaction::TYPE_SETTLEMENT_GAIN,
                ShareTransaction::TYPE_SETTLEMENT_LOSS,
            ], true)) {
                continue;
            }

            $amount = $this->toCents($transaction->total_amount);
            $quantity = (int) $transaction->kitta;

            if ($transaction->transaction_type === ShareTransaction::TYPE_BUY) {
                $kitta += $quantity;
                $observedInvestment += $amount;
                $correctedInvestment += $amount;
            } elseif ($transaction->transaction_type === ShareTransaction::TYPE_WITHDRAW) {
                $basis = $correctedInvestment;
                $kitta -= $quantity;
                $observedInvestment -= $amount;
                $correctedInvestment -= $amount;

                if ($kitta === 0) {
                    $linked = $settlements->get($transaction->id, collect());
                    if ($linked->count() > 1) {
                        $safe = false;
                        $reasons[] = "withdrawal {$transaction->id} has duplicate settlements";
                    } elseif ($linked->count() === 1) {
                        $settlement = $linked->first();
                        $expectedType = $correctedInvestment >= 0
                            ? ShareTransaction::TYPE_SETTLEMENT_GAIN
                            : ShareTransaction::TYPE_SETTLEMENT_LOSS;
                        if (
                            $settlement->status !== 'active'
                            || $settlement->shareholder_id !== $shareholder->id
                            || $settlement->transaction_type !== $expectedType
                            || $this->toCents($settlement->total_amount) !== abs($correctedInvestment)
                        ) {
                            $safe = false;
                            $reasons[] = "withdrawal {$transaction->id} has an inconsistent settlement";
                        }
                        $observedInvestment = 0;
                        $correctedInvestment = 0;
                    } else {
                        $residual = $correctedInvestment;
                        $candidates[] = [
                            'withdrawal' => $transaction,
                            'basis_cents' => $basis,
                            'withdrawal_cents' => $amount,
                            'residual_cents' => $residual,
                            'type' => $residual === 0 ? null : ($residual > 0
                                ? ShareTransaction::TYPE_SETTLEMENT_GAIN
                                : ShareTransaction::TYPE_SETTLEMENT_LOSS),
                        ];
                        $correctedInvestment = 0;
                    }
                } elseif ($correctedInvestment < 0) {
                    $safe = false;
                    $reasons[] = "withdrawal {$transaction->id} creates negative investment before full settlement";
                }
            } elseif ($transaction->transaction_type === ShareTransaction::TYPE_TRANSFER) {
                if ((int) $transaction->shareholder_id === $shareholder->id) {
                    $kitta -= $quantity;
                    $observedInvestment -= $amount;
                    $correctedInvestment -= $amount;
                }
                if ((int) $transaction->to_shareholder_id === $shareholder->id) {
                    $kitta += $quantity;
                    $observedInvestment += $amount;
                    $correctedInvestment += $amount;
                }
            } else {
                $safe = false;
                $reasons[] = "transaction {$transaction->id} has unsupported type {$transaction->transaction_type}";
            }

            if ($kitta < 0 || $correctedInvestment < 0) {
                $safe = false;
                $reasons[] = "transaction {$transaction->id} creates an impossible negative position";
            }
        }

        $currentInvestment = $this->toCents($shareholder->total_investment);
        if ((int) $shareholder->kitta !== $kitta) {
            $safe = false;
            $reasons[] = 'current Kitta does not match reconstructed active history';
        }
        if (! in_array($currentInvestment, [$observedInvestment, $correctedInvestment], true)) {
            $safe = false;
            $reasons[] = 'current investment does not match reconstructed history';
        }
        if ($correctedInvestment < 0 || $correctedInvestment % 100 !== 0) {
            $safe = false;
            $reasons[] = 'corrected investment cannot be stored exactly in the shareholder master';
        }

        return [
            'shareholder' => $shareholder,
            'candidates' => $candidates,
            'safe' => $safe,
            'reason' => implode('; ', array_unique($reasons)),
            'current_kitta' => (int) $shareholder->kitta,
            'current_investment_cents' => $currentInvestment,
            'calculated_kitta' => $kitta,
            'corrected_investment_cents' => $correctedInvestment,
        ];
    }

    private function toCents(mixed $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
