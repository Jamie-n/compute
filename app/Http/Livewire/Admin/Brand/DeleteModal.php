<?php

namespace App\Http\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;

class DeleteModal extends Component
{
    public const SHOW = 'show-delete';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public Brand $brand;

    public function mount()
    {
        $this->brand = Brand::make();
    }

    public function show(Brand $brand)
    {
        $this->brand = $brand;

        $this->hidden = false;
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function delete()
    {
        $this->hidden = true;

        $this->brand->delete();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function render()
    {
        return view('livewire.admin.brand.delete-modal');
    }
}
