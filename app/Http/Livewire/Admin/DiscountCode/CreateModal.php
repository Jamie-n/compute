<?php

namespace App\Http\Livewire\Admin\DiscountCode;

use App\Http\Livewire\Traits\UpdatesDiscountCodeStartEndDate;
use App\Models\DiscountCode;
use DB;
use Livewire\Component;

class CreateModal extends Component
{
    use UpdatesDiscountCodeStartEndDate;

    public const SHOW = 'show-create';

    protected $listeners = [self::SHOW => 'show'];

    public DiscountCode $discountCode;

    public $code_active_start = '';
    public $code_active_end = '';

    public bool $hidden = true;

    protected $rules = [
        'discountCode.code' => ['required', 'max:30'],
        'discountCode.discount_percentage' => ['required', 'integer', 'max:100'],
        'code_active_start' => ['required', 'date'],
        'code_active_end' => ['required', 'date', 'after:code_active_start'],
    ];

    public function mount()
    {
        $this->discountCode = DiscountCode::make();
    }

    public function show()
    {
        $this->hidden = false;

        $this->reset('code_active_start', 'code_active_end');
        $this->discountCode = DiscountCode::make();
    }

    public function hide()
    {
        $this->hidden = true;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function (){
            $this->updateStartEndDate($this->discountCode, $this->code_active_start, $this->code_active_end);

            $this->discountCode->save();
        });

        $this->emitTo(IndexTable::class, IndexTable::REFRESH);

        $this->hide();
    }

    public function render()
    {
        return view('livewire.admin.discount-code.create-modal');
    }
}
