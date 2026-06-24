<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/Categories', [
            'categories' => ProductCategory::where('product_type', 'source_code')
                                           ->withCount('products')
                                           ->latest()
                                           ->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ProductCategory::create($this->validated($request));

        return back()->with('success', 'Kategori dibuat.');
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category->id));

        return back()->with('success', 'Kategori diperbarui.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Kategori dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_categories,slug'.($ignoreId ? ','.$ignoreId : '')],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['product_type'] = 'source_code';

        return $data;
    }
}
