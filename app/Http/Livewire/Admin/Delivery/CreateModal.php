<?php

namespace App\Http\Livewire\Admin\Delivery;

use App\Models\DeliveryType;
use Livewire\Component;

class CreateModal extends Component
{
    public const SHOW = 'show-create';

    protected $listeners = [self::SHOW => 'show'];

    public DeliveryType $deliveryType;

    public bool $hidden = true;

    protected $rules = [
        'deliveryType.name' => ['required', 'max:30'],
        'deliveryType.description' => ['nullable', 'max:255'],
        'deliveryType.price' => ['required', 'numeric'],
    ];

    public function mount()
    {
        $this->deliveryType = DeliveryType::make();
    }

    public function show()
    {
        $this->hidden = false;

        $this->deliveryType = DeliveryType::make();
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function save()
    {
        $this->validate();

        $this->deliveryType->save();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);

        $this->hide();
    }

    public function render()
    {
        return view('livewire.admin.delivery.create-modal');
    }
}
