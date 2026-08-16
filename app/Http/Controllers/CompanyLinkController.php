<?php

namespace App\Http\Controllers;

use App\Models\CompanyLink;
use Illuminate\Http\Request;

class CompanyLinkController extends Controller
{
    public function index()
    {
        $links = CompanyLink::latest()->get();

        return view('admin.company-links.index', compact('links'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
        ]);

        CompanyLink::create($validated);

        return redirect()
            ->route('company-links.index')
            ->with('success', 'Link added successfully.');
    }

    public function update(Request $request, CompanyLink $companyLink)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $companyLink->update($validated);

        return redirect()
            ->route('company-links.index')
            ->with('success', 'Link updated successfully.');
    }

    public function destroy(CompanyLink $companyLink)
    {
        $companyLink->delete();

        return redirect()
            ->route('company-links.index')
            ->with('success', 'Link deleted successfully.');
    }
}