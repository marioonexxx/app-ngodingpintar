<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categories'])
            ->where('vendor_id', $request->user()->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('product_category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Frontend::render('Vendor/Products/Index', [
            'products' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['status', 'category_id', 'search']),
            'categories' => ProductCategory::where('is_active', true)->where('product_type', 'source_code')->orderBy('name')->get(),
            'statuses' => [Product::STATUS_DRAFT, Product::STATUS_PENDING, Product::STATUS_ACTIVE, Product::STATUS_REJECTED],
        ]);
    }

    public function create()
    {
        return Frontend::render('Vendor/Products/Create', [
            'categories' => ProductCategory::where('is_active', true)->where('product_type', 'source_code')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['vendor_id'] = $request->user()->id;
        $data['status'] = Product::STATUS_PENDING;

        $product = Product::create($data);
        $product->categories()->sync($data['product_category_ids'] ?? []);

        return to_route('vendor.products.index')->with('success', 'Produk berhasil dibuat dan sedang menunggu persetujuan admin.');
    }

    public function edit(Request $request, Product $product)
    {
        abort_if($product->vendor_id !== $request->user()->id, 403);

        return Frontend::render('Vendor/Products/Edit', [
            'product' => $product,
            'categories' => ProductCategory::where('is_active', true)->where('product_type', 'source_code')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_if($product->vendor_id !== $request->user()->id, 403);

        $data = $this->validated($request, $product->id);

        if (! array_key_exists('thumbnail', $data) && ! $product->thumbnail) {
            $data['thumbnail'] = $this->defaultThumbnail();
        }

        $data['status'] = Product::STATUS_PENDING;
        $data['rejection_reason'] = null; // Clear rejection reason if any

        $product->update($data);
        $product->categories()->sync($data['product_category_ids'] ?? []);

        return to_route('vendor.products.index')->with('success', 'Produk diperbarui dan dikembalikan ke status pending untuk ditinjau ulang oleh admin.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        abort_if($product->vendor_id !== $request->user()->id, 403);

        $product->delete();

        return back(303)->with('success', 'Produk dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'product_category_ids' => ['required', 'array', 'min:1'],
            'product_category_ids.*' => ['exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'screenshots' => [$ignoreId ? 'nullable' : 'required', 'array', 'min:3', 'max:5'],
            'screenshots.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'file_path' => ['nullable', 'string', 'max:255'],
            'installation_guide_file' => ['nullable', 'file', 'mimes:pdf,txt,md,doc,docx', 'max:10240'],
            'demo_url' => ['nullable', 'url', 'max:255'],
            'download_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['name'], $ignoreId);

        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $this->storeThumbnail($request);
        } elseif (! $ignoreId) {
            $data['thumbnail'] = $this->defaultThumbnail();
        }

        if ($request->hasFile('screenshots')) {
            $data['screenshots'] = $this->storeScreenshots($request);
        }

        if ($request->hasFile('installation_guide_file')) {
            $data['installation_guide'] = $this->storeInstallationGuide($request);
        }

        unset($data['thumbnail_file'], $data['installation_guide_file']);

        return $data;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function storeThumbnail(Request $request): string
    {
        $directory = public_path('img/products');
        File::ensureDirectoryExists($directory);

        $file = $request->file('thumbnail_file');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return "img/products/{$filename}";
    }

    private function storeScreenshots(Request $request): array
    {
        $directory = public_path('img/products/screenshots');
        File::ensureDirectoryExists($directory);

        $paths = [];

        foreach ($request->file('screenshots', []) as $file) {
            if (! $file) {
                continue;
            }

            $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
            $file->move($directory, $filename);
            $paths[] = "img/products/screenshots/{$filename}";
        }

        return $paths;
    }

    private function storeInstallationGuide(Request $request): string
    {
        $directory = public_path('files/installation-guides');
        File::ensureDirectoryExists($directory);

        $file = $request->file('installation_guide_file');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return "files/installation-guides/{$filename}";
    }

    private function defaultThumbnail(): string
    {
        return 'img/thumbnail/default-thumbnail.png';
    }
}
