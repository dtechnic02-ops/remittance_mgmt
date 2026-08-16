<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use Illuminate\Http\Request;

class CompanyInfoController extends Controller
{
    public function edit()
    {
        $companyInfo = CompanyInfo::first();

        return view('admin.company-info.edit', compact('companyInfo'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'image' => ['nullable', 'image', 'max:5120'],
            'thumbnail_image' => ['nullable', 'image', 'max:5120'],
            'location' => ['nullable', 'string'],
            'mobile_number' => ['nullable', 'string', 'max:50'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'staff_mobile_number' => ['nullable', 'string', 'max:50'],
            'staff_email' => ['nullable', 'email', 'max:255'],
            'staff_photo' => ['nullable', 'image', 'max:5120'],
            'staff_title' => ['nullable', 'string', 'max:255'],
            'description_1' => ['nullable', 'string'],
            'description_2' => ['nullable', 'string'],
        ]);

        $companyInfo = CompanyInfo::firstOrNew();

        foreach (['logo', 'image', 'thumbnail_image', 'staff_photo'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)
                    ->store('company-info', 'public');
            } else {
                unset($validated[$field]);
            }
        }

        $companyInfo->fill($validated);
        $companyInfo->save();

        return redirect()
            ->route('company-info.edit')
            ->with('success', 'Company information saved successfully.');
    }
}