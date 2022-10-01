<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BasketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;
use App\Http\Livewire\Admin\Brand\IndexTable as BrandsIndexTable;
use App\Http\Livewire\Admin\Customer\IndexTable as CustomerIndexTable;
use App\Http\Livewire\Admin\Delivery\IndexTable as DeliveryIndexTable;
use App\Http\Livewire\Admin\DiscountCode\IndexTable as DiscountCodeIndexTable;
use App\Http\Livewire\Admin\Product\IndexTable as ProductIndexTable;
use App\Http\Livewire\Admin\Warehouse\IndexTable as ShippingIndexTable;
use App\Http\Livewire\Admin\User\IndexTable as AdminIndexTable;
use App\Support\Enums\Permissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

//Laravel Socialite OAuth Routes
Route::prefix('/auth')->name('socialite.')->group(function () {
    Route::get('/redirect/{provider}', [LoginController::class, 'socialiteRedirect'])->name('redirect');
    Route::get('/callback', [LoginController::class, 'socialiteAuthenticate'])->name('authenticate-callback');
});

Route::get('/', [StorefrontController::class, 'index'])->name('storefront.index');

Route::get('products/{product}', [ProductController::class, 'show'])->name('product.show');

Route::resource('/user', UserController::class);

Route::prefix('basket')->group(function () {
    Route::get('/', [BasketController::class, 'index'])->name('basket.index');
    Route::post('add/{product}', [BasketController::class, 'addToBasket'])->name('basket.add-product');
    Route::post('reduce/{product}', [BasketController::class, 'reduceQuantityInBasket'])->name('basket.reduce-product');
    Route::post('remove/{product}', [BasketController::class, 'removeFromBasket'])->name('basket.remove-product');
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');

    Route::Resource('order', OrderController::class)->except('create', 'store');

    Route::middleware("permission:" . Permissions::ADMIN->value)->name('admin.')->prefix('/admin')->group(function () {
        Route::view('/', 'admin.index')->name('index');

        Route::middleware('can:' . Permissions::MANAGE_USERS->value)->prefix('/users')->group(function () {
            Route::get('/admins', AdminIndexTable::class)->name('users.admins.index');
            Route::get('/customers', CustomerIndexTable::class)->name('users.customers.index');
        });

        Route::middleware('can:' . Permissions::MANAGE_SHIPPING->value)->group(function () {
            Route::get('/warehouse', ShippingIndexTable::class)->name('shipping.index');
            Route::get('/delivery', DeliveryIndexTable::class)->name('delivery.index');
        });

        Route::middleware('can:' . Permissions::MANAGE_PRODUCTS->value)->group(function () {
            Route::get('/brand', BrandsIndexTable::class)->name('brand.index');
            Route::get('/products', ProductIndexTable::class)->name('products.index');
            Route::get('/discount-codes', DiscountCodeIndexTable::class)->name('discount-codes.index');
        });


        //Product image filepond routes
        Route::prefix('product-image')->middleware('can:' . Permissions::MANAGE_PRODUCTS->value)->group(function () {
            Route::post('/upload', [ProductImageController::class, 'upload'])->name('product-image.upload');
            Route::post('/revert', [ProductImageController::class, 'revert'])->name('product-image.revert');
            Route::get('/load/{image_name?}', [ProductImageController::class, 'load'])->name('product-image.load');
            Route::delete('remove/{image_name?}', [ProductImageController::class, 'remove'])->name('product-image.remove');
        });
    });
});

Route::view('/confirmation', 'order.confirmation')->name('order.confirmation');

Route::get('{category?}', [StorefrontController::class, 'show'])->name('storefront.show');
