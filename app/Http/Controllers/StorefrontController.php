<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class StorefrontController extends Controller
{
    public function index()
    {
        $products = Product::latest()
            ->paginate(config('pagination.product_index_page_length'));

        return view('storefront.index')->with('products', $products)->with('title', 'Latest Products');
    }

    public function show(Category $category)
    {
        $products = Product::byCategory($category)
            ->orderBy('name')
            ->paginate(config('pagination.category_show_page_length'));

        return view('storefront.show')->with('products', $products)->with('category', $category);
    }
}
