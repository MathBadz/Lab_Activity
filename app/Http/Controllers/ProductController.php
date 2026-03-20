<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /api/products — Returns the list of products.
     * Supports optional ?search= query parameter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->select(['id', 'name', 'category', 'image', 'price', 'stock']);

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $products = $query
            ->orderByRaw('CASE WHEN stock = 0 THEN 1 ELSE 0 END')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }
}
