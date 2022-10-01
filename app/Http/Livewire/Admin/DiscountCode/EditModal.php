<?php

namespace App\Http\Livewire\Admin\DiscountCode;

use App\Http\Livewire\Traits\UpdatesDiscountCodeStartEndDate;
use App\Models\DiscountCode;
use Livewire\Component;

class EditModal extends Component
{
    use UpdatesDiscountCodeStartEndDate;

    public const SHOW = 'show-edit';

    protected $listeners = [self::SHOW => 'show'];

    public bool $hidden = true;

    public DiscountCode $discountCode;

    public $code_active_start = '';
    public $code_active_end = '';

    protected $rules = [
        'code_active_start' => ['required', 'date'],
        'code_active_end' => ['required', 'date', 'after:code_active_start'],
    ];

    public function mount()
    {
        $this->discountCode = DiscountCode::make();
    }

    public function show(DiscountCode $discountCode)
    {
        $this->discountCode = $discountCode;

        $this->code_active_start = $discountCode->code_active_start->format('Y-m-d');
        $this->code_active_end = $discountCode->code_active_end->format('Y-m-d');

        $this->hidden = false;
    }

    public function save()
    {
        $this->validate();

        $this->updateStartEndDate($this->discountCode, $this->code_active_start, $this->code_active_end);

        $this->discountCode->save();

        $this->reset('code_active_start', 'code_active_end');

        $this->hide();

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function render()
    {
        return view('livewire.admin.discount-code.edit-modal');
    }
}

