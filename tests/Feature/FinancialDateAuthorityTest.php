<?php

namespace Tests\Feature;

use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Services\FinancialDateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialDateAuthorityTest extends TestCase
{
    use RefreshDatabase;

    public function test_ashadh_and_shrawan_use_the_correct_nepali_financial_year(): void
    {
        $dates = app(FinancialDateService::class);
        $ashadhAd = LaravelNepaliDate::from('2083-03-31')->toEnglishDate('Y-m-d', 'en');
        $shrawanAd = LaravelNepaliDate::from('2083-04-01')->toEnglishDate('Y-m-d', 'en');

        $this->assertSame('2083-03-31', $dates->fromEnglishDate($ashadhAd)['date_bs']);
        $this->assertSame('2082/83', $dates->fromEnglishDate($ashadhAd)['financial_year']);
        $this->assertSame('2083-04-01', $dates->fromEnglishDate($shrawanAd)['date_bs']);
        $this->assertSame('2083/84', $dates->fromEnglishDate($shrawanAd)['financial_year']);
    }

    public function test_financial_store_ignores_tampered_bs_date_and_financial_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $account = Account::create([
            'name' => 'Main Cash', 'code' => 'CASH-AUTH', 'type' => Account::TYPE_CASH,
            'opening_balance' => 5000, 'current_balance' => 5000, 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $category = ExpenseCategory::create([
            'name' => 'Date authority', 'code' => 'DATE-AUTH', 'is_active' => true,
            'created_by' => $admin->id, 'updated_by' => $admin->id,
        ]);
        $dateAd = LaravelNepaliDate::from('2083-04-01')->toEnglishDate('Y-m-d', 'en');

        $response = $this->actingAs($admin)->post(route('expenses.store'), [
            'expense_category_id' => $category->id,
            'account_id' => $account->id,
            'date_ad' => $dateAd,
            'date_bs' => '2099-12-30',
            'financial_year' => '2099/00',
            'amount' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'date_ad' => $dateAd.' 00:00:00',
            'date_bs' => '2083-04-01',
            'financial_year' => '2083/84',
            'amount' => 100,
        ]);
    }
}
