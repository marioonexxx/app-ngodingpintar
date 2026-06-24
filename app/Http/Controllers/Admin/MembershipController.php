<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/Memberships', [
            'memberships' => Membership::latest()->paginate(15),
            'users' => \App\Models\User::where('role', '!=', 'admin')->latest('last_login_at')->paginate(15, ['*'], 'users_page'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Membership::create($this->validated($request));

        return back()->with('success', 'Membership dibuat.');
    }

    public function update(Request $request, Membership $membership): RedirectResponse
    {
        $membership->update($this->validated($request, $membership->id));

        return back()->with('success', 'Membership diperbarui.');
    }

    public function destroy(Membership $membership): RedirectResponse
    {
        try {
            $membership->delete();
            return back(303)->with('success', 'Membership dihapus.');
        } catch (\Exception $e) {
            return back(303)->withErrors(['error' => 'Membership gagal dihapus karena masih terhubung dengan data lain.']);
        }
    }

    public function destroyUser(\App\Models\User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            abort(403, 'Admin tidak dapat dihapus melalui menu ini.');
        }
        
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                // Delete related records manually to prevent foreign key constraint issues
                // and to ensure no orphaned data remains
                $transactionIds = $user->transactions()->withTrashed()->pluck('id');
                
                \App\Models\PaymentLog::whereIn('transaction_id', $transactionIds)->delete();
                
                $user->transactions()->withTrashed()->forceDelete();
                $user->carts()->delete();
                $user->testimonials()->delete();
                $user->customFeatureRequests()->delete();
                $user->coachingBookings()->delete();
                
                if ($user->vendor) {
                    $user->vendor()->delete();
                }
                if ($user->mentor) {
                    $user->mentor()->delete();
                }

                $user->forceDelete();
            });

            return back(303)->with('success', 'Member beserta seluruh data transaksinya berhasil dihapus permanen.');
        } catch (\Exception $e) {
            return back(303)->withErrors(['error' => 'Member gagal dihapus. ' . $e->getMessage()]);
        }
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        if (!$request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->name)]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:memberships,slug'.($ignoreId ? ','.$ignoreId : '')],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'benefits' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
