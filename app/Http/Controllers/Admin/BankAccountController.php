<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/BankAccounts', [
            'bankAccounts' => BankAccount::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        BankAccount::create($this->validated($request));

        return back()->with('success', 'Rekening bank ditambahkan.');
    }

    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->update($this->validated($request));

        return back()->with('success', 'Rekening bank diperbarui.');
    }

    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return back()->with('success', 'Rekening bank dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'bank_name' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
