<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Models\Permission;
use App\Models\Shareholder;
use App\Models\ShareTransaction;
use App\Models\User;
use App\Services\ShareTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;
use Tests\TestCase;

class ShareTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_preserves_decimal_per_kitta_and_exact_total(): void
    {
        $admin = $this->user('admin', 'decimal-transfer@example.test');
        $sender = $this->shareholder(null, 'DECIMAL-FROM', 20);
        $receiver = $this->shareholder(null, 'DECIMAL-TO', 0);

        $transfer = $this->transfer($sender, $receiver, 10, $admin, '950.20');

        $this->assertSame('950.20', $transfer->per_kitta_value);
        $this->assertSame('9502.00', $transfer->total_amount);
    }

    public function test_admin_staff_shareholder_and_guest_access_matrix(): void
    {
        $admin = $this->user('admin', 'admin-transfer@example.test');
        $staff = $this->user('staff', 'staff-transfer@example.test');
        $ownerUser = $this->user('shareholder', 'owner-transfer@example.test');
        $this->shareholder($ownerUser, 'OWNER', 10);
        $this->shareholder(null, 'TARGET', 5);

        $this->actingAs($admin)->get(route('share-transfers.index'))
            ->assertRedirect(route('share-transactions.index', ['type' => 'transfer']));
        $this->actingAs($admin)->get(route('share-transactions.index'))->assertOk();
        $this->actingAs($admin)->get(route('share-transfers.create'))->assertRedirect();
        $this->actingAs($admin)
            ->get(route('share-transactions.create', ['type' => 'transfer']))
            ->assertOk();

        $this->actingAs($staff)->get(route('share-transactions.index'))->assertForbidden();
        $this->grant($staff, 'share-transfer.view');
        $this->actingAs($staff)->get(route('share-transactions.index', ['type' => 'transfer']))
            ->assertOk()->assertDontSee('+ New Share Transfer');
        $this->actingAs($staff)->get(route('share-transactions.create', ['type' => 'transfer']))
            ->assertForbidden();
        $this->grant($staff, 'share-transfer.create');
        $this->actingAs($staff)->get(route('share-transfers.create'))->assertRedirect();
        $this->actingAs($staff)
            ->get(route('share-transactions.create', ['type' => 'transfer']))
            ->assertOk();

        $this->actingAs($ownerUser)->get(route('share-transactions.index', ['type' => 'transfer']))
            ->assertOk()->assertSee('+ New Share Transaction');
        $this->actingAs($ownerUser)->get(route('share-transfers.create'))->assertRedirect();
        $this->actingAs($ownerUser)
            ->get(route('share-transactions.create', ['type' => 'transfer']))
            ->assertOk();

        auth()->logout();
        $this->get(route('share-transactions.index'))->assertRedirect(route('login'));
    }

    public function test_shareholder_cannot_spoof_sender_and_portal_marks_sent_and_received(): void
    {
        $senderUser = $this->user('shareholder', 'sender@example.test');
        $receiverUser = $this->user('shareholder', 'receiver@example.test');
        $sender = $this->shareholder($senderUser, 'SENDER', 10);
        $receiver = $this->shareholder($receiverUser, 'RECEIVER', 2);
        $spoofed = $this->shareholder(null, 'SPOOFED', 20);

        $response = $this->actingAs($senderUser)->post(route('share-transfers.store'), [
            'shareholder_id' => $spoofed->id,
            'to_shareholder_id' => $receiver->id,
            'date_ad' => '2026-08-18',
            'date_bs' => '2000-01-01',
            'financial_year' => '2000/01',
            'kitta' => 3,
            'per_kitta_value' => 700,
        ]);

        $transfer = ShareTransaction::where('transaction_type', ShareTransaction::TYPE_TRANSFER)->firstOrFail();
        $response->assertRedirect(route('share-transactions.show', $transfer));
        $this->assertSame($sender->id, $transfer->shareholder_id);
        $this->assertSame($receiver->id, $transfer->to_shareholder_id);
        $this->assertSame(7, $sender->fresh()->kitta);
        $this->assertSame(5, $receiver->fresh()->kitta);
        $this->assertSame(20, $spoofed->fresh()->kitta);

        $this->actingAs($senderUser)->get(route('shareholder.dashboard'))
            ->assertOk()->assertSee('New Transfer')->assertSee('SENT')->assertSee('RECEIVER Name');
        $this->actingAs($receiverUser)->get(route('shareholder.dashboard'))
            ->assertOk()->assertSee('RECEIVED')->assertSee('SENDER Name');
    }

    public function test_shareholder_sees_only_related_transfers(): void
    {
        $viewerUser = $this->user('shareholder', 'viewer@example.test');
        $viewer = $this->shareholder($viewerUser, 'VIEWER', 10);
        $otherA = $this->shareholder(null, 'OTHER-A', 10);
        $otherB = $this->shareholder(null, 'OTHER-B', 10);

        $own = $this->transfer($viewer, $otherA, 1, $viewerUser);
        $private = $this->transfer($otherA, $otherB, 1, $viewerUser);

        $this->actingAs($viewerUser)->get(route('share-transactions.index', ['type' => 'transfer']))
            ->assertOk()
            ->assertSee($own->transaction_number)
            ->assertDontSee($private->transaction_number);
    }

    public function test_excessive_and_same_party_transfers_are_atomic(): void
    {
        $admin = $this->user('admin', 'atomic@example.test');
        $sender = $this->shareholder(null, 'ATOMIC-FROM', 2);
        $receiver = $this->shareholder(null, 'ATOMIC-TO', 4);
        $service = app(ShareTransferService::class);

        foreach ([
            [$sender->id, $receiver->id, 3],
            [$sender->id, $sender->id, 1],
        ] as [$from, $to, $kitta]) {
            try {
                $service->create($this->payload($from, $to, $kitta, $admin));
                $this->fail('Expected transfer to be rejected.');
            } catch (RuntimeException) {
                $this->assertSame(2, $sender->fresh()->kitta);
                $this->assertSame(4, $receiver->fresh()->kitta);
                $this->assertSame(0, ShareTransaction::where('transaction_type', 'transfer')->count());
            }
        }
    }

    public function test_transfer_has_no_account_or_ledger_effect_and_number_uses_record_id(): void
    {
        $admin = $this->user('admin', 'financial-transfer@example.test');
        $sender = $this->shareholder(null, 'FIN-FROM', 8);
        $receiver = $this->shareholder(null, 'FIN-TO', 1);
        $cash = $this->account('TRANSFER-CASH', Account::TYPE_CASH, 5000);
        $bank = $this->account('TRANSFER-BANK', Account::TYPE_BANK, 9000);

        $transfer = $this->transfer($sender, $receiver, 2, $admin, 750);

        $this->assertNull($transfer->account_id);
        $this->assertSame('SHR-'.str_pad((string) $transfer->id, 6, '0', STR_PAD_LEFT), $transfer->transaction_number);
        $this->assertSame(5000, $cash->fresh()->current_balance);
        $this->assertSame(9000, $bank->fresh()->current_balance);
        $this->assertDatabaseCount('ledger_entries', 0);
        $this->assertSame(6500, $sender->fresh()->total_investment);
        $this->assertSame(2500, $receiver->fresh()->total_investment);
        $this->assertSame('750.00', $transfer->per_kitta_value);
        $this->assertSame('1500.00', $transfer->total_amount);
    }

    public function test_transfer_appears_in_unified_history_and_nullable_account_detail_is_safe(): void
    {
        $admin = $this->user('admin', 'history@example.test');
        $sender = $this->shareholder(null, 'HISTORY-FROM', 5);
        $receiver = $this->shareholder(null, 'HISTORY-TO', 1);
        $account = $this->account('HISTORY-CASH', Account::TYPE_CASH, 1000);
        $buy = ShareTransaction::create([
            'transaction_number' => 'SHR-HISTORY-BUY',
            'transaction_type' => ShareTransaction::TYPE_BUY,
            'shareholder_id' => $sender->id,
            'account_id' => $account->id,
            'date_ad' => '2026-08-18', 'date_bs' => '2083-05-02', 'financial_year' => '2083/84',
            'kitta' => 1, 'per_kitta_value' => 1000, 'total_amount' => 1000,
            'status' => 'active', 'created_by' => $admin->id,
        ]);
        $transfer = $this->transfer($sender, $receiver, 1, $admin);

        $this->actingAs($admin)->get(route('share-transactions.index'))
            ->assertOk()->assertSee($buy->transaction_number)->assertSee($transfer->transaction_number);
        $this->actingAs($admin)->get(route('share-transactions.show', $buy))->assertOk();
        $this->actingAs($admin)->get(route('share-transactions.show', $transfer))->assertOk();
    }

    public function test_filtered_share_transactions_show_totals_and_support_print_and_excel(): void
    {
        $admin = $this->user('admin', 'share-export@example.test');
        $sender = $this->shareholder(null, 'EXPORT-FROM', 5);
        $receiver = $this->shareholder(null, 'EXPORT-TO', 1);
        $transfer = $this->transfer($sender, $receiver, 2, $admin, 750);
        $filters = [
            'type' => ShareTransaction::TYPE_TRANSFER,
            'financial_year' => 'all',
            'status' => 'active',
        ];

        $index = $this->actingAs($admin)->get(route('share-transactions.index', $filters))->assertOk();
        $this->assertSame(1, $index->viewData('totalRecords'));
        $this->assertSame(0, $index->viewData('totalAmount'));
        $index->assertSee('Total Records:')->assertSee('Total Amount / Value:')->assertSee('Print A4')->assertSee('Export Excel');

        $this->actingAs($admin)->get(route('share-transactions.index', $filters + ['output' => 'print']))
            ->assertOk()
            ->assertSee('Filtered Share Transactions Report')
            ->assertSee($transfer->transaction_number)
            ->assertSee('1,500');

        $response = $this->actingAs($admin)
            ->get(route('share-transactions.index', $filters + ['output' => 'excel']))
            ->assertOk();
        $this->assertStringContainsString('share-transactions-filtered-', (string) $response->headers->get('content-disposition'));
        $sheet = IOFactory::load($response->baseResponse->getFile()->getPathname())->getActiveSheet();
        $this->assertSame($transfer->transaction_number, $sheet->getCell('A2')->getValue());
        $this->assertSame(1500.0, $sheet->getCell('J2')->getValue());
    }

    public function test_transfer_cancellation_uses_unified_route_and_reverses_kitta_once(): void
    {
        $admin = $this->user('admin', 'cancel-transfer@example.test');
        $sender = $this->shareholder(null, 'CANCEL-FROM', 6);
        $receiver = $this->shareholder(null, 'CANCEL-TO', 2);
        $transfer = $this->transfer($sender, $receiver, 2, $admin, 650);

        $this->actingAs($admin)
            ->from(route('share-transactions.show', $transfer))
            ->post(route('share-transactions.cancel', $transfer), ['cancellation_reason' => 'Incorrect'])
            ->assertRedirect(route('share-transactions.show', $transfer))
            ->assertSessionHas('success');

        $this->assertSame('cancelled', $transfer->fresh()->status);
        $this->assertSame(6, $sender->fresh()->kitta);
        $this->assertSame(2, $receiver->fresh()->kitta);
        $this->assertSame(6000, $sender->fresh()->total_investment);
        $this->assertSame(2000, $receiver->fresh()->total_investment);
        $this->assertDatabaseCount('ledger_entries', 0);
    }

    public function test_transfer_attachment_authorization_uses_permission_and_ownership(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin', 'attachment-admin@example.test');
        $senderUser = $this->user('shareholder', 'attachment-sender@example.test');
        $receiverUser = $this->user('shareholder', 'attachment-receiver@example.test');
        $otherUser = $this->user('shareholder', 'attachment-other@example.test');
        $staff = $this->user('staff', 'attachment-staff@example.test');
        $sender = $this->shareholder($senderUser, 'ATT-FROM', 5);
        $receiver = $this->shareholder($receiverUser, 'ATT-TO', 1);
        $this->shareholder($otherUser, 'ATT-OTHER', 1);
        $path = UploadedFile::fake()->create('proof.pdf', 5, 'application/pdf')
            ->store('share-transfers/attachments', 'local');
        $transfer = app(ShareTransferService::class)->create([
            ...$this->payload($sender->id, $receiver->id, 1, $admin),
            'attachment' => $path,
        ]);

        $this->actingAs($admin)->get(route('share-transfers.attachment', $transfer))->assertOk();
        $this->actingAs($senderUser)->get(route('share-transfers.attachment', $transfer))->assertOk();
        $this->actingAs($receiverUser)->get(route('share-transfers.attachment', $transfer))->assertOk();
        $this->actingAs($otherUser)->get(route('share-transfers.attachment', $transfer))->assertForbidden();
        $this->actingAs($staff)->get(route('share-transfers.attachment', $transfer))->assertForbidden();
        $this->grant($staff, 'share-transfer.view');
        $this->actingAs($staff)->get(route('share-transfers.attachment', $transfer))->assertOk();
    }

    private function transfer(
        Shareholder $from,
        Shareholder $to,
        int $kitta,
        User $actor,
        int|float|string $perKittaValue = 1000
    ): ShareTransaction
    {
        return app(ShareTransferService::class)->create(
            $this->payload($from->id, $to->id, $kitta, $actor, $perKittaValue)
        );
    }

    private function payload(
        int $from,
        int $to,
        int $kitta,
        User $actor,
        int|float|string $perKittaValue = 1000
    ): array
    {
        return [
            'shareholder_id' => $from, 'to_shareholder_id' => $to,
            'date_ad' => '2026-08-18', 'date_bs' => '2083-05-02',
            'financial_year' => '2083/84', 'kitta' => $kitta,
            'per_kitta_value' => $perKittaValue,
            'created_by' => $actor->id,
        ];
    }

    private function user(string $role, string $email): User
    {
        return User::factory()->create([
            'role' => $role, 'email' => $email, 'is_active' => true, 'email_verified_at' => now(),
        ]);
    }

    private function shareholder(?User $user, string $code, int $kitta): Shareholder
    {
        return Shareholder::create([
            'user_id' => $user?->id, 'code' => $code, 'name' => $code.' Name',
            'kitta' => $kitta, 'per_kitta_value' => 1000,
            'total_investment' => $kitta * 1000, 'is_active' => true,
        ]);
    }

    private function account(string $code, string $type, int $balance): Account
    {
        return Account::create([
            'name' => $code, 'code' => $code, 'type' => $type,
            'opening_balance' => $balance, 'current_balance' => $balance, 'is_active' => true,
        ]);
    }

    private function grant(User $user, string $code): void
    {
        $permission = Permission::where('code', $code)->firstOrFail();
        $user->permissions()->syncWithoutDetaching([$permission->id => ['assigned_at' => now()]]);
    }
}
