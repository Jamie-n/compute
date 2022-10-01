<?php

namespace App\Http\Livewire\Admin\Brand;

use App\Models\Brand;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditModal extends Component
{
    public const SHOW = 'show-edit';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public Brand $brand;

    protected function rules()
    {
        return [
            'brand.name' => ['required', 'string', 'max:255'],
            'brand.slug' => ['required', 'string', 'max:255', Rule::unique('brands', 'slug')->ignore($this->brand->id ?? null, 'id')]
        ];
    }

    public function mount()
    {
        $this->brand = Brand::make();
    }

    public function show(Brand $brand)
    {
        $this->brand = $brand;

        $this->hidden = false;
    }

    public function save()
    {
        $this->validate();

        $this->brand->save();

        $this->hide();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function render()
    {
        return view('livewire.admin.brand.edit-modal');
    }
}
