<?php

namespace App\Http\Livewire\Admin\Product;

use App\Models\Product;
use App\Models\User;
use Livewire\Component;

class DeleteModal extends Component
{
    public const SHOW = 'show-delete';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public Product $product;

    public function mount()
    {
        $this->product = Product::make();
    }

    public function show(Product $product)
    {
        $this->product = $product;

        $this->hidden = false;
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function delete()
    {
        $this->hidden = true;

        $this->product->delete();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function render()
    {
        return view('livewire.admin.product.delete-modal');
    }
}
