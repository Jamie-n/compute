<?php

namespace App\Http\Livewire\Admin\DiscountCode;

use App\Models\DiscountCode;
use Livewire\Component;

class DeleteModal extends Component
{
    public const SHOW = 'show-delete';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public DiscountCode $discountCode;

    public function mount()
    {
        $this->discountCode = DiscountCode::make();
    }

    public function show(DiscountCode $discountCode)
    {
        $this->discountCode = $discountCode;

        $this->hidden = false;
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function delete()
    {
        $this->hidden = true;

        $this->discountCode->delete();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function render()
    {
        return view('livewire.admin.discount-code.delete-modal');
    }
}
