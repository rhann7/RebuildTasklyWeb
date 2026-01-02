<?php

namespace App\Http\Controllers\Companies;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::with(['companyOwner', 'companyCategory']);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('company_category_id', $request->category);
        }

        return Inertia::render('companies/index', [
            'companies'  => $query->latest()->paginate(12)->withQueryString(),
            'categories' => CompanyCategory::query()->select(['id', 'name'])->orderBy('id')->get(),
            'filters'    => $request->only(['search', 'category']),
        ]);
    }

    public function show(Company $company)
    {
        $company->load(['companyOwner', 'companyCategory']);
        
        return inertia('companies/show', [
            'company' => $company
        ]);
    }

    public function edit(Company $company)
    {
        return Inertia::render('companies/edit', [
            'company'    => $company->load('companyCategory'),
            'categories' => CompanyCategory::all(),
        ]);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => ['required', 'email', Rule::unique('companies')->ignore($company->id)],
            'phone'               => 'required|string|max:20',
            'address'             => 'required|string',
            'company_category_id' => 'required|exists:company_categories,id',
            'is_active'           => 'boolean',
        ]);

        $company->update($validated);
        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->back()->with('success', 'Company deleted successfully.');
    }
}