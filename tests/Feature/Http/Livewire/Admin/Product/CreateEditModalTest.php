<?php

namespace Tests\Feature\Http\Livewire\Admin\Product;

use App\Http\Livewire\Admin\Product\CreateEditModal;
use App\Http\Livewire\Admin\Product\IndexTable;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Support\Enums\UserRoles;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class CreateEditModalTest extends TestCase
{
    public function test_can_create_new_product_with_no_image()
    {
        $product = Product::factory()->stock(10)->make();

        Livewire::test(CreateEditModal::class)
            ->set('product.name', $product->name)
            ->set('product.slug', $product->slug)
            ->set('product.brand_id', $product->brand_id)
            ->set('product.description', $product->description)
            ->set('product.price', $product->price)
            ->set('product.stock_quantity', $product->stock_quantity)
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted(IndexTable::REFRESH);

        $this->assertDatabaseHas(Product::class, $product->only(['name', 'slug', 'brand_id', 'description', 'price', 'stock_quantity']));
    }

    /**
     * Tests the process of creating a new product with an attached image.
     * As filepond uses javascript for file uploading, the functionality has been emulated
     * in this test to represent the actions taken when attaching a file to a product.
     */
    public function test_can_create_new_product_with_image()
    {
        $user = User::factory()->create();
        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        $testImageName = 'test-img.jpg';

        Storage::fake(config('filesystems.default_disk.product.temporary'));
        Storage::fake(config('filesystems.default_disk.product.storage'));

        //Emulate what happens when a new file is uploaded via filepond
        $response = $this->post(route('admin.product-image.upload'), ['filepond' => UploadedFile::fake()->image($testImageName)]);
        $response->assertSuccessful();

        $fileName = $response->getContent();

        self::assertTrue(Storage::disk(config('filesystems.default_disk.product.temporary'))->exists($fileName));

        $product = Product::factory()->stock(15)->make();

        Livewire::test(CreateEditModal::class)
            ->set('product.name', $product->name)
            ->set('product.slug', $product->slug)
            ->set('product.brand_id', $product->brand_id)
            ->set('product.description', $product->description)
            ->set('product.price', $product->price)
            ->set('product.stock_quantity', $product->stock_quantity)
            ->set('file', $fileName)
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted(IndexTable::REFRESH);

        self::assertFalse(Storage::disk(config('filesystems.default_disk.product.temporary'))->exists($fileName));

        $savedProduct = Product::whereName($product->name)->first();
        self::assertTrue(!is_null($savedProduct->image));

        self::assertTrue(Storage::disk(config('filesystems.default_disk.product.storage'))->exists($savedProduct->image));
    }

    public function test_resets_file_array_after_when_re_opening_modal()
    {
        Livewire::test(CreateEditModal::class)
            ->set('file', 'test-file')
            ->call('hide')
            ->emit(CreateEditModal::SHOW_CREATE)
            ->assertSet('file', '');
    }

    /**
     * Tests the process of updating a product image.
     * As filepond uses javascript for file uploading, the functionality has been emulated
     * in this test to represent the actions taken when attaching a file to a product.
     */
    public function test_can_update_product_image()
    {
        $user = User::factory()->create();
        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        Storage::fake(config('filesystems.default_disk.product.temporary'));
        Storage::fake(config('filesystems.default_disk.product.storage'));

        $startingImgName = 'test-img.jpg';

        $product = Product::factory()->stock(10)->create(['image' => $startingImgName]);

        //Emulate what happens when a new file is uploaded via filepond
        $response = $this->post(route('admin.product-image.upload'), ['filepond' => UploadedFile::fake()->image('new-img.jpg')]);

        $fileName = $response->getContent();

        $this->withoutExceptionHandling();

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->set('file', $fileName)
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted(IndexTable::REFRESH);

        $product->refresh();

        self::assertTrue(Storage::disk(config('filesystems.default_disk.product.storage'))->exists($product->image));
    }

    /**
     * Tests the process of removing a product image.
     * As filepond uses javascript for file uploading, the functionality has been emulated
     * in this test to represent the actions taken when attaching a file to a product.
     */
    public function test_can_remove_product_image()
    {
        $user = User::factory()->create();
        $user->assignRole(UserRoles::SYSTEM_ADMIN->value);
        $this->be($user);

        Storage::fake(config('filesystems.default_disk.product.temporary'));
        Storage::fake(config('filesystems.default_disk.product.storage'));

        $startingImgName = 'test-img.jpg';

        $product = Product::factory()->stock(10)->create(['image' => $startingImgName]);

        //Emulate what happens when a new file is uploaded via filepond
        $response = $this->post(route('admin.product-image.upload'), ['filepond' => UploadedFile::fake()->image('new-img.jpg')]);

        $fileName = $response->getContent();

        $this->withoutExceptionHandling();

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->set('file', $fileName)
            ->call('save')
            ->assertHasNoErrors()
            ->assertEmitted(IndexTable::REFRESH);

        $product->refresh();

        self::assertTrue(Storage::disk(config('filesystems.default_disk.product.storage'))->exists($product->image));

        $this->delete(route('admin.product-image.remove', $product->image))
            ->assertSuccessful();

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->call('unsetImage')
            ->call('save');

        self::assertFalse(Storage::disk(config('filesystems.default_disk.product.storage'))->exists($product->image));

        $product->refresh();

        self::assertNull($product->image);
    }

    public function test_shows_correct_title_and_button_text_when_creating_new_product()
    {
        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_CREATE)
            ->assertSet('title', 'Create New Product')
            ->assertSet('buttonText', 'Create Product');
    }

    public function test_hidden_is_false_when_emitting_show_create_event()
    {
        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_CREATE)
            ->assertSet('hidden', false);
    }

    public function test_dispatches_load_modal_browser_event_when_show_create_is_called()
    {
        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_CREATE)
            ->assertDispatchedBrowserEvent(CreateEditModal::INITIALIZE_JS);
    }

    public function test_shows_correct_title_and_button_text_when_editing_product()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_EDIT, $product)
            ->assertSet('title', "Edit Product: {$product->name}")
            ->assertSet('buttonText', 'Update Product');
    }

    public function test_hidden_is_set_to_false_after_emitting_show_edit_event()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_EDIT, $product)
            ->assertSet('hidden', false);
    }

    public function test_dispatches_load_modal_browser_event_when_show_edit_is_called()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_EDIT, $product)
            ->assertDispatchedBrowserEvent(CreateEditModal::INITIALIZE_JS);
    }

    public function test_dispatches_destroy_browser_event_when_hide_is_called()
    {
        Livewire::test(CreateEditModal::class)
            ->call('hide')
            ->assertDispatchedBrowserEvent(CreateEditModal::DESTROY_JS)
            ->set('hidden', true);
    }

    public function test_hidden_is_set_to_true_when_hide_called()
    {
        Livewire::test(CreateEditModal::class)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_product_slug_is_updated_when_the_product_name_is_changed()
    {
        $productName = 'test product 1';

        Livewire::test(CreateEditModal::class)
            ->updateProperty('product.name', $productName)
            ->assertSet('product.slug', Str::slug($productName));
    }

    public function test_correctly_resets_category_array_when_switching_between_edit_and_create_modal()
    {
        $product = Product::factory()->stock(10)->create();
        $category = Category::factory()->create();

        $product->categories()->attach($category);

        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_EDIT, $product)
            ->assertSet('categories', [$category->id])
            ->call('hide')
            ->emit(CreateEditModal::SHOW_CREATE)
            ->assertSet('categories', []);
    }

    public function test_correctly_resets_category_array_when_switching_between_create_and_edit_modal()
    {
        $product = Product::factory()->stock(10)->create();
        $category = Category::factory()->create();

        $category2 = Category::factory()->create();

        $product->categories()->attach($category);

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_CREATE)
            ->set('categories', [$category2->id])
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->assertSet('categories', [$category->id]);
    }

    public function test_correctly_resets_validation_when_switching_between_edit_and_create_modal()
    {
        $product = Product::factory()->stock(10)->create(['name' => '']);
        $category = Category::factory()->create();

        $product->categories()->attach($category);

        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_EDIT, $product)
            ->call('save')
            ->assertHasErrors()
            ->call('hide')
            ->emit(CreateEditModal::SHOW_CREATE)
            ->assertHasNoErrors();
    }

    public function test_correctly_resets_validation_when_switching_between_create_and_edit_modal()
    {
        $product = Product::factory()->stock(10)->create(['name' => '']);
        $category = Category::factory()->create();

        $product->categories()->attach($category);

        Livewire::test(CreateEditModal::class)->emit(CreateEditModal::SHOW_CREATE)
            ->call('save')
            ->assertHasErrors()
            ->call('hide')
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->assertHasNoErrors();
    }

    /**
     * @test
     * @dataProvider data_that_should_fail
     */
    public function test_validation_rules_fail($key, $value, $rule)
    {
        Livewire::test(CreateEditModal::class)
            ->set($key, $value)
            ->call('save')
            ->assertHasErrors([$key => $rule]);
    }

    public function data_that_should_fail(): array
    {
        return [
            'product name required' => ['product.name', '', 'required'],
            'product name too long' => ['product.name', Str::random(256), 'max:255'],
            'product slug required ' => ['product.slug', '', 'required'],
            'product slug too long' => ['product.slug', Str::random(256), 'max:255'],
            'product brand required' => ['product.brand_id', '', 'required'],
            'product description required' => ['product.description', '', 'required'],
            'product description too long' => ['product.description', Str::random(1001), 'max:1000'],
            'product price required' => ['product.price', '', 'required'],
            'product price must be numeric' => ['product.price', 'abc', 'numeric'],
            'product discount percentage integer' => ['product.discount_percentage', 1.5, 'integer'],
            'product discount max 100' => ['product.discount_percentage', 150, 'max:100'],
            'product stock quantity is required' => ['product.stock_quantity', '', 'required'],
            'product stock quantity must be an integer' => ['product.stock_quantity', 'abc', 'integer'],
            'product stock quantity must be above 1' => ['product.stock_quantity', 0, 'min:1']
        ];
    }

    /**
     * @test
     * @dataProvider data_that_should_pass
     */
    public function test_validation_rules_pass($key, $value, $rule)
    {
        Livewire::test(CreateEditModal::class)
            ->set($key, $value)
            ->call('save')
            ->assertHasNoErrors([$key => $rule]);
    }

    public function data_that_should_pass(): array
    {
        return [
            'product name required' => ['product.name', 'a', 'required'],
            'product name is max length' => ['product.name', Str::random(255), 'max:255'],
            'product slug required ' => ['product.slug', 'a', 'required'],
            'product slug is max length' => ['product.slug', Str::random(255), 'max:255'],
            'product brand required' => ['product.brand_id', 'a', 'required'],
            'product description required' => ['product.description', 'a', 'required'],
            'product description is max length' => ['product.description', Str::random(1000), 'max:1000'],
            'product price required' => ['product.price', 'a', 'required'],
            'product price is a float' => ['product.price', 1.10, 'numeric'],
            'product discount percentage nullable' => ['product.discount_percentage', '', 'nullable'],
            'product discount percentage integer' => ['product.discount_percentage', 10, 'integer'],
            'product discount max 100' => ['product.discount_percentage', 100, 'max:100'],
            'product discount less than max' => ['product.discount_percentage', 99, 'max:100'],
            'product stock quantity is required' => ['product.stock_quantity', 'a', 'required'],
            'product stock quantity is an integer' => ['product.stock_quantity', 1000, 'integer'],
            'product stock quantity must be above 1' => ['product.stock_quantity', 1, 'min:1']
        ];
    }

    public function test_slug_unique_validation_fails_if_not_unique()
    {
        $slug = 'test-slug';
        Product::factory()->stock(10)->create(['slug' => $slug]);

        Livewire::test(CreateEditModal::class)
            ->set('product.slug', $slug)
            ->call('save')
            ->assertHasErrors(['product.slug' => 'unique']);
    }

    public function test_slug_unique_validation_passes_unique()
    {
        $slug = 'test-slug';
        Livewire::test(CreateEditModal::class)
            ->set('product.slug', $slug)
            ->call('save')
            ->assertHasNoErrors(['product.slug' => 'unique']);
    }

    public function test_slug_unique_validation_passes_on_update()
    {
        $product = Product::factory()->stock(10)->create(['slug' => 'old-product-slug']);

        $newSlug = 'new-product-slug';

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->set('product.slug', $newSlug)
            ->call('save')
            ->assertHasNoErrors(['product.slug' => 'unique']);
    }

    public function test_brand_id_validation_fails_if_doesnt_exist()
    {
        Livewire::test(CreateEditModal::class)
            ->set('product.brand_id', 1)
            ->call('save')
            ->assertHasErrors(['product.brand_id' => 'exists']);
    }

    public function test_brand_id_validation_passes_if_brand_exists()
    {
        $brand = Brand::factory()->create();

        Livewire::test(CreateEditModal::class)
            ->set('product.brand_id', $brand->id)
            ->call('save')
            ->assertHasNoErrors(['product.brand_id' => 'exists']);
    }

    public function test_can_hide_modal()
    {
        Brand::factory()->create();

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_CREATE)
            ->assertSet('hidden', false)
            ->call('hide')
            ->assertSet('hidden', true);
    }

    public function test_can_update_product_discount_amount()
    {
        $product = Product::factory()->stock(10)->create();

        Livewire::test(CreateEditModal::class)
            ->emit(CreateEditModal::SHOW_EDIT, $product)
            ->set('product.discount_percentage', 15)
            ->call('save');

        $product->refresh();

        self::assertEquals(15, $product->discount_percentage);
    }
}
