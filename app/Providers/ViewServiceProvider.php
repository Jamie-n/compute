<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeliveryType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Spatie\Permission\Models\Role;


class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //Check to ensure that the nav bar does not remain empty if the application was booted before the categories table was populated as this would cache nothing
        if (Cache::has('categories') && Cache::get('categories')->isEmpty()) {
            Cache::forget('categories');
        }

        Cache::rememberForever('categories', function () {
            try {
                return Category::orderBy('order')->get();
            } catch (\Exception $e) {
                return collect();
            }
        });

        ViewFacade::composer(['layouts.partials.nav', 'livewire.admin.product.create-edit-modal'], function (View $view) {
            return $view->with('categories', Cache::get('categories'));
        });

        ViewFacade::composer('livewire.admin.product.create-edit-modal', function (View $view) {
            return $view->with('brands', Brand::pluck('name', 'id')->prepend('Select a Brand', 0));
        });

        ViewFacade::composer('livewire.checkout.checkout-card', function (View $view) {
            return $view->with('deliveryTypes', DeliveryType::all()->pluck('displayText', 'id'));
        });
    }
}
