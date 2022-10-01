<?php

namespace App\Http\Livewire\Admin\Brand;

use App\Models\Brand;
use Livewire\Component;

class CreateModal extends Component
{
    public const SHOW = 'show-create';

    protected $listeners = [self::SHOW => 'show'];

    public Brand $brand;

    public bool $hidden = true;

    protected $rules = [
        'brand.name' => ['required', 'max:255'],
        'brand.slug' => ['required', 'max:255', 'unique:brands,slug'],

    ];

    public function mount()
    {
        $this->brand = Brand::make();
    }

    public function show()
    {
        $this->hidden = false;

        $this->brand = Brand::make();
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function save()
    {
        $this->validate();

        $this->brand->save();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);

        $this->hidden = true;
    }

    public function render()
    {
        return view('livewire.admin.brand.create-modal');
    }
}
