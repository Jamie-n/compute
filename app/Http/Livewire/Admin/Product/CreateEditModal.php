<?php

namespace App\Http\Livewire\Admin\Product;

use App\Http\Livewire\Traits\HandleUploadedFiles;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class CreateEditModal extends Component
{
    use HandleUploadedFiles;

    public const SHOW_CREATE = 'show-create';
    public const SHOW_EDIT = 'show-edit';
    public const INITIALIZE_JS = 'initialize-js';
    public const DESTROY_JS = 'destroy-js';

    protected $listeners = [self::SHOW_CREATE => 'showCreate', self::SHOW_EDIT => 'showEdit'];

    public Product $product;

    public $hidden = true;

    public array $categories = [];

    public string $buttonText = '';
    public string $title = '';

    protected function rules()
    {
        return [
            'product.name' => ['required', 'max:255'],
            'product.slug' => ['required', 'max:255', Rule::unique('products', 'slug')->ignore($this->product->id ?? null, 'id')->withoutTrashed()],
            'product.brand_id' => ['required', 'exists:brands,id'],
            'product.description' => ['required', 'max:1000'],
            'product.price' => ['required', 'numeric'],
            'product.discount_percentage' => ['nullable', 'integer', 'max:100'],
            'product.stock_quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    protected $validationAttributes = [
        'product.brand_id' => 'brand'
    ];

    public function mount()
    {
        $this->product = Product::make();
    }

    public function updatingProductName($value)
    {
        $this->product->slug = Str::slug($value);
    }

    public function resetProperties()
    {
        $this->reset('categories', 'title', 'buttonText', 'file');
        $this->clearValidation();
    }

    public function show()
    {
        $this->hidden = false;

        $this->dispatchBrowserEvent('initialize-js', self::generateFilesJson());
    }

    public function hide()
    {
        $this->dispatchBrowserEvent('destroy-js');
        $this->hidden = true;

        $this->resetProperties();
    }

    public function showEdit(Product $product)
    {
        $this->product = $product;

        $this->title = "Edit Product: {$product->name}";
        $this->buttonText = 'Update Product';

        $this->categories = $product->categories()->pluck('id')->toArray();

        $this->show();
    }

    public function showCreate()
    {
        $this->mount();

        $this->title = "Create New Product";
        $this->buttonText = 'Create Product';

        $this->show();
    }

    public function generateFilesJson(): array
    {
        if (!isset($this->product->id) || is_null($this->product->image))
            return [];

        return [
            [
                'source' => $this->product->image,
                'options' => [
                    'type' => 'local'
                ],
            ],
        ];
    }

    public function updatedProductDiscountPercentage($value)
    {
        if ($value === '')
            $this->product->discount_percentage = 0;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $this->handleAttachedFiles(
                $this->product,
                config('filesystems.default_disk.product.temporary'),
                config('filesystems.default_disk.product.storage'),
                Product::generateProductImageName($this->product->slug)
            );

            $this->product->save();

            $this->product->categories()->sync($this->categories);
        });

        $this->hide();

        $this->emit(IndexTable::REFRESH);
    }

    public function unsetImage()
    {
        $this->product->update(['image' => null]);
    }

    public function render()
    {
        return view('livewire.admin.product.create-edit-modal');
    }
}
