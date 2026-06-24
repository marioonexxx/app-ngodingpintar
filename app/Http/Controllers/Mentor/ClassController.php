<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\Frontend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'membership', 'classProduct'])
            ->withCount('classBatches')
            ->where('product_type', 'class')
            ->where('mentor_id', $request->user()->id)
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', fn($q) => $q->where('product_categories.id', $request->category_id));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        return Frontend::render('Mentor/Classes/Index', [
            'products' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['status', 'category_id', 'search', 'sort']),
            'categories' => ProductCategory::where('is_active', true)->where('product_type', 'class')->orderBy('name')->get(),
            'statuses' => [Product::STATUS_DRAFT, Product::STATUS_PENDING, Product::STATUS_ACTIVE, Product::STATUS_REJECTED, Product::STATUS_ARCHIVED],
        ]);
    }

    public function create()
    {
        return Frontend::render('Mentor/Classes/Create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $classData = $this->validatedClassProduct($request);
        $data = $this->validated($request);
        $data['product_type'] = 'class';
        $data['mentor_id'] = $request->user()->id;
        $data['status'] = Product::STATUS_PENDING;

        DB::transaction(function () use ($data, $classData): void {
            $product = Product::create($data);
            $product->categories()->sync($data['product_category_ids'] ?? []);
            $product->classBatches()->create([
                ...$classData,
                'batch_number' => 1,
                'status' => \App\Models\ClassBatch::STATUS_OPEN,
            ]);
        });

        return redirect()->route('mentor.classes.index')->with('success', 'Produk kelas berhasil dibuat dan menunggu persetujuan admin.');
    }

    public function edit(Request $request, Product $class)
    {
        abort_if($class->product_type !== 'class', 404);
        abort_if($class->mentor_id !== $request->user()->id, 403);
        
        $class->load(['categories', 'classProduct']);
        
        return Frontend::render('Mentor/Classes/Edit', array_merge($this->formData(), [
            'product' => $class,
        ]));
    }

    public function update(Request $request, Product $class): RedirectResponse
    {
        abort_if($class->product_type !== 'class', 404);
        abort_if($class->mentor_id !== $request->user()->id, 403);

        $classData = $this->validatedClassProduct($request);
        $data = $this->validated($request, $class->id);
        $data['product_type'] = 'class';

        if (! array_key_exists('thumbnail', $data) && ! $class->thumbnail) {
            $data['thumbnail'] = $this->defaultThumbnail();
        }

        $data['status'] = Product::STATUS_PENDING;
        $data['rejection_reason'] = null;

        DB::transaction(function () use ($class, $data, $classData): void {
            $class->update($data);
            $class->categories()->sync($data['product_category_ids'] ?? []);
            
            if ($class->classProduct) {
                $class->classProduct->update($classData);
            }
        });

        return redirect()->route('mentor.classes.index')->with('success', 'Produk kelas diperbarui dan dikembalikan ke status pending untuk ditinjau ulang oleh admin.');
    }

    public function destroy(Request $request, Product $class): RedirectResponse
    {
        abort_if($class->product_type !== 'class', 404);
        abort_if($class->mentor_id !== $request->user()->id, 403);

        $class->delete();

        return back()->with('success', 'Produk kelas dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'product_category_ids' => ['nullable', 'array'],
            'product_category_ids.*' => ['exists:product_categories,id'],
            'membership_id' => ['nullable', 'exists:memberships,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'thumbnail_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'screenshots' => ['nullable', 'array', 'max:5'],
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

    private function validatedClassProduct(Request $request): array
    {
        return $request->validate([
            'schedule_type' => ['nullable', 'in:fixed,recurring'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'recurring_days' => ['nullable', 'array'],
            'platform' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ]);
    }

    private function formData(): array
    {
        return [
            'categories' => ProductCategory::where('is_active', true)->where('product_type', 'class')->orderBy('name')->get(),
            'memberships' => Membership::where('is_active', true)->orderBy('name')->get(),
            'statuses' => [Product::STATUS_DRAFT, Product::STATUS_PENDING, Product::STATUS_ACTIVE, Product::STATUS_REJECTED, Product::STATUS_ARCHIVED],
        ];
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
