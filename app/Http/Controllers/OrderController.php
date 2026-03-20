<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    /**
     * POST /api/order — Places an order.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer',
            'full_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $result = $this->orderService->placeOrder(
            $validated['product_id'],
            $validated['quantity'],
        );

        if ($result['success']) {
            return response()->json([
                'result' => $result['result'],
                'message' => $result['message'],
                'product' => $result['product'],
                'remainingStock' => $result['remainingStock'],
            ], $result['status']);
        }

        return response()->json([
            'result' => $result['result'],
            'error' => $result['message'],
        ], $result['status']);
    }
}
