<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function store(Request $request)
    {
        $product = Product::create($request->all());

        $this->exportData();
        return response()->json(['success' => true]);
    }

    public function fetch()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        $totalValueSum = $products->sum(fn($product) => $product->quantity_in_stock * $product->price_per_item);

        return response()->json(['products' => $products, 'total' => $totalValueSum]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'product_name' => $request->input('product_name'),
            'quantity_in_stock' => $request->input('quantity_in_stock'),
            'price_per_item' => $request->input('price_per_item'),
        ]);

        $this->exportData();
        return response()->json(['success' => true]);
    }

    private function exportData()
    {
        $products = Product::all();
        Storage::put('public/products.json', $products->toJson(JSON_PRETTY_PRINT));
    }
}
