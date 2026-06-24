<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PromoCodeController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/PromoCodes', [
            'promoCodes' => PromoCode::query()->latest()->paginate(15),
            'discountOptions' => PromoCode::DISCOUNT_OPTIONS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $promoCode = PromoCode::create($data);

        if ($data['is_active']) {
            PromoCode::whereKeyNot($promoCode->id)->update(['is_active' => false]);
        }

        return back()->with('success', 'Kode promo dibuat.');
    }

    public function update(Request $request, PromoCode $promoCode): RedirectResponse
    {
        $data = $this->validated($request, $promoCode->id);
        $promoCode->update($data);

        if ($data['is_active']) {
            PromoCode::whereKeyNot($promoCode->id)->update(['is_active' => false]);
        }

        return back()->with('success', 'Kode promo diperbarui.');
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        $promoCode->delete();

        return back()->with('success', 'Kode promo dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $discounts = implode(',', PromoCode::DISCOUNT_OPTIONS);

        if ($request->filled('code')) {
            $request->merge(['code' => Str::upper(Str::slug($request->input('code')))]);
        }

        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:50', Rule::unique('promo_codes', 'code')->ignore($ignoreId)],
            'discount_percent' => ['required', 'integer', "in:{$discounts}"],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['code'] = $this->normalizeCode($data['code'] ?? null, $ignoreId);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }

    private function normalizeCode(?string $code, ?int $ignoreId = null): string
    {
        $code = Str::upper(Str::slug($code ?: $this->generatedCode()));

        while (
            PromoCode::query()
                ->where('code', $code)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $code = $this->generatedCode();
        }

        return $code;
    }

    private function generatedCode(): string
    {
        return 'MDV-'.Str::upper(Str::random(8));
    }
}
