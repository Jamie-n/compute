<?php

namespace App\Http\Livewire\Admin\Delivery;

use App\Models\DeliveryType;
use Livewire\Component;

class DeleteModal extends Component
{
    public const SHOW = 'show-delete';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public DeliveryType $deliveryType;

    public function mount()
    {
        $this->deliveryType = DeliveryType::make();
    }

    public function show(DeliveryType $discountCode)
    {
        $this->deliveryType = $discountCode;

        $this->hidden = false;
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function delete()
    {
        $this->hidden = true;

        $this->deliveryType->delete();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function render()
    {
        return view('livewire.admin.delivery.delete-modal');
    }
}
