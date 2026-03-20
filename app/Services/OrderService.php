<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Process an order with business rule validation.
     *
     * @return array{success: bool, result: string, message: string, remaining_stock?: int, status: int}
     */
    public function placeOrder(int $productId, int $quantity): array
    {
        // Business Rule: Quantity must be greater than 0 and cannot be negative
        if ($quantity === 0) {
            return [
                'success' => false,
                'result' => 'Invalid request',
                'message' => 'Invalid quantity',
                'status' => 400,
            ];
        }

        if ($quantity < 0) {
            return [
                'success' => false,
                'result' => 'Error',
                'message' => 'Invalid quantity',
                'status' => 400,
            ];
        }

        return DB::transaction(function () use ($productId, $quantity): array {
            // Business Rule: Product must exist
            $product = Product::query()
                ->whereKey($productId)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                return [
                    'success' => false,
                    'result' => 'Error response',
                    'message' => 'Product not found',
                    'status' => 404,
                ];
            }

            // Business Rule: If stock is 0, order must be rejected
            // Business Rule: Quantity must not exceed available stock
            if ($product->stock < $quantity) {
                return [
                    'success' => false,
                    'result' => 'Order rejected',
                    'message' => 'Not enough stock',
                    'status' => 400,
                ];
            }

            // Business Rule: If order is valid, stock must be reduced
            $product->stock -= $quantity;
            $product->save();

            return [
                'success' => true,
                'result' => 'Stock updated',
                'message' => 'Order successful',
                'product' => $product->name,
                'remainingStock' => $product->stock,
                'status' => 200,
            ];
        });
    }
}
