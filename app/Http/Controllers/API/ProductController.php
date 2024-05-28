<?php
namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $product = Product::create($validatedData);
        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if ($product) {
            return response()->json($product, 200);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stock' => 'sometimes|required|integer',
        ]);

        $product = Product::find($id);
        if ($product) {
            $product->update($validatedData);
            return response()->json($product, 200);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json($product, 204);
        }
        return response()->json(['error' => 'Product not found'], 404);
    }
}
