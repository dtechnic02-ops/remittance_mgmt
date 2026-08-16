<?php

namespace Tests\Feature;

use App\Models\CompanyInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_public_company_page(): void
    {
        CompanyInfo::create([
            'company_name' => 'Asha Enterprises',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Asha Enterprises');
        $response->assertSee('Login');
    }
}