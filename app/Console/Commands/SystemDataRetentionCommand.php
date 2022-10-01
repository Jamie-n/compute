<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\DeliveryType;
use App\Models\DiscountCode;
use App\Models\Product;
use Illuminate\Console\Command;

class SystemDataRetentionCommand extends Command
{
    protected $signature = 'system:data-retention';

    protected $description = 'Delete soft deleted models which have been deleted for 3 years or more';

    public function handle()
    {
        $cutoff = now()->subYears(3);

        Brand::whereDate('deleted_at', '>=', $cutoff)->forceDelete();

        DeliveryType::whereDate('deleted_at', '>=', $cutoff)->forceDelete();

        DiscountCode::whereDate('deleted_at', '>=', $cutoff)->forceDelete();

        Product::whereDate('deleted_at', '>=', $cutoff)->forceDelete();


    }
}
