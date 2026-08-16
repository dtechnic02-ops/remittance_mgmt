<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\CompanyLink;

class PublicPageController extends Controller
{
    public function index()
    {
        $companyInfo = CompanyInfo::first();
        $companyLinks = CompanyLink::all();

        return view('auth.login', compact('companyInfo', 'companyLinks'));
    }
}