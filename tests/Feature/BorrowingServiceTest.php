<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Borrowing;
use App\Models\Lender;
use App\Models\User;
use App\Services\BorrowingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class BorrowingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrow_increases_selected_account_balance(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 50000,
            'current_balance' => 50000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $borrowing = $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 100000,
            'reference' => 'TEST-BORROW',
            'created_by' => $user->id,
        ]);

        $cash->refresh();

        $this->assertSame(
            150000,
            $cash->current_balance
        );

        $this->assertSame(
            100000,
            $service->outstanding($lender->id)
        );

        $this->assertSame(
            'BOR-000001',
            $borrowing->transaction_number
        );

        $this->assertDatabaseHas('ledger_entries', [
            'account_id' => $cash->id,
            'transaction_type' => 'borrowing',
            'transaction_id' => $borrowing->id,
            'direction' => 'increase',
            'amount' => 100000,
            'component' => 'borrow',
        ]);
    }

    public function test_repay_decreases_account_and_outstanding(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 50000,
            'current_balance' => 50000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 100000,
            'created_by' => $user->id,
        ]);

        $repayment = $service->create([
            'transaction_type' => Borrowing::TYPE_REPAY,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-13',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'amount' => 40000,
            'created_by' => $user->id,
        ]);

        $cash->refresh();

        $this->assertSame(
            110000,
            $cash->current_balance
        );

        $this->assertSame(
            60000,
            $service->outstanding($lender->id)
        );

        $this->assertDatabaseHas('ledger_entries', [
            'transaction_type' => 'borrowing',
            'transaction_id' => $repayment->id,
            'direction' => 'decrease',
            'amount' => 40000,
            'component' => 'repay',
        ]);
    }

    public function test_repay_cannot_exceed_lender_outstanding(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 100000,
            'current_balance' => 100000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 50000,
            'created_by' => $user->id,
        ]);

        try {
            $service->create([
                'transaction_type' => Borrowing::TYPE_REPAY,
                'lender_id' => $lender->id,
                'account_id' => $cash->id,
                'date_ad' => '2026-08-13',
                'date_bs' => '2083-04-28',
                'financial_year' => '2083/84',
                'amount' => 60000,
                'created_by' => $user->id,
            ]);

            $this->fail(
                'Expected RuntimeException was not thrown.'
            );
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'Repayment amount cannot exceed lender outstanding balance.',
                $exception->getMessage()
            );
        }

        $this->assertSame(
            50000,
            $service->outstanding($lender->id)
        );

        $this->assertSame(
            150000,
            $cash->fresh()->current_balance
        );

        $this->assertDatabaseCount(
            'borrowings',
            1
        );
    }

    public function test_repay_rolls_back_when_account_balance_is_insufficient(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 0,
            'current_balance' => 0,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 100000,
            'created_by' => $user->id,
        ]);

        /*
         * Simulate that the borrowed money has already
         * been used elsewhere.
         */
        $cash->update([
            'current_balance' => 10000,
        ]);

        try {
            $service->create([
                'transaction_type' => Borrowing::TYPE_REPAY,
                'lender_id' => $lender->id,
                'account_id' => $cash->id,
                'date_ad' => '2026-08-13',
                'date_bs' => '2083-04-28',
                'financial_year' => '2083/84',
                'amount' => 50000,
                'created_by' => $user->id,
            ]);

            $this->fail(
                'Expected RuntimeException was not thrown.'
            );
        } catch (RuntimeException $exception) {
            $this->assertNotEmpty(
                $exception->getMessage()
            );
        }

        $this->assertSame(
            10000,
            $cash->fresh()->current_balance
        );

        $this->assertSame(
            100000,
            $service->outstanding($lender->id)
        );

        $this->assertDatabaseCount(
            'borrowings',
            1
        );
    }

    public function test_repayment_cancellation_restores_account_and_outstanding(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 50000,
            'current_balance' => 50000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 100000,
            'created_by' => $user->id,
        ]);

        $repayment = $service->create([
            'transaction_type' => Borrowing::TYPE_REPAY,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-13',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'amount' => 40000,
            'created_by' => $user->id,
        ]);

        $this->assertSame(
            60000,
            $service->outstanding($lender->id)
        );

        $service->cancel(
            $repayment,
            $user->id,
            'Wrong repayment'
        );

        $repayment->refresh();
        $cash->refresh();

        $this->assertSame(
            100000,
            $service->outstanding($lender->id)
        );

        $this->assertSame(
            150000,
            $cash->current_balance
        );

        $this->assertSame(
            'cancelled',
            $repayment->status
        );
    }

    public function test_borrow_cannot_be_cancelled_after_part_of_it_is_repaid(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $cash = Account::create([
            'name' => 'Main Cash',
            'code' => 'CASH01',
            'type' => Account::TYPE_CASH,
            'opening_balance' => 50000,
            'current_balance' => 50000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $borrow = $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 100000,
            'created_by' => $user->id,
        ]);

        $service->create([
            'transaction_type' => Borrowing::TYPE_REPAY,
            'lender_id' => $lender->id,
            'account_id' => $cash->id,
            'date_ad' => '2026-08-13',
            'date_bs' => '2083-04-28',
            'financial_year' => '2083/84',
            'amount' => 40000,
            'created_by' => $user->id,
        ]);

        $this->expectException(RuntimeException::class);

        $service->cancel(
            $borrow,
            $user->id,
            'Cancel borrow'
        );
    }

    public function test_fixed_deposit_cannot_be_used_for_borrowing(): void
    {
        $user = User::factory()->create();

        $lender = Lender::create([
            'code' => 'LND-001',
            'name' => 'Ram Store',
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $fixedDeposit = Account::create([
            'name' => 'Fixed Deposit',
            'code' => 'FD01',
            'type' => Account::TYPE_FIXED_DEPOSIT,
            'opening_balance' => 100000,
            'current_balance' => 100000,
            'allow_negative' => false,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $service = app(BorrowingService::class);

        $this->expectException(RuntimeException::class);

        $service->create([
            'transaction_type' => Borrowing::TYPE_BORROW,
            'lender_id' => $lender->id,
            'account_id' => $fixedDeposit->id,
            'date_ad' => '2026-08-12',
            'date_bs' => '2083-04-27',
            'financial_year' => '2083/84',
            'amount' => 100000,
            'created_by' => $user->id,
        ]);
    }
}