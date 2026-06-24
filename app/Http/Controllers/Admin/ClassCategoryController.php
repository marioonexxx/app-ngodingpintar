<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassCategoryController extends Controller
{
    public function index()
    {
        return Frontend::render('Admin/ClassCategories', [
            'categories' => ProductCategory::where('product_type', 'class')
                                            ->withCount('products')
                                            ->latest()
                                            ->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ProductCategory::create($this->validated($request));

        return back()->with('success', 'Kategori Kelas dibuat.');
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category->id));

        return back()->with('success', 'Kategori Kelas diperbarui.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Kategori Kelas dihapus.');
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
        $data['product_type'] = 'class'; // Always class

        return $data;
    }
}
