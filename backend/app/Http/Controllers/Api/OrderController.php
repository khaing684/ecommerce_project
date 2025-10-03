<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * Only accessible by authenticated admins.
     */
    public function index(Request $request)
    {
        try {
            // Verify user is admin
            if (!$request->user() instanceof \App\Models\Admin) {
                return response()->json([
                    'error' => 'Access denied. Admin privileges required.'
                ], Response::HTTP_FORBIDDEN);
            }

            $orders = Order::with(['admin', 'orderItems.product'])
                          ->orderBy('created_at', 'desc')
                          ->get();
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve orders',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Can be accessed publicly for customer orders or by admins.
     */
    public function store(Request $request)
    {
        try {
            // Check if user is authenticated admin or public access
            $isAdmin = $request->user() instanceof \App\Models\Admin;
            
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'shipping_address' => 'required|string',
                'payment_method' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);

            return DB::transaction(function () use ($validated, $request, $isAdmin) {
                // Calculate total amount
                $totalAmount = 0;
                $orderItemsData = [];
                
                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $lineTotal = $product->price * $item['quantity'];
                    $totalAmount += $lineTotal;
                    
                    $orderItemsData[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $product->price
                    ];
                }

                // Create order
                $orderData = [
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'total_amount' => $totalAmount,
                    'shipping_address' => $validated['shipping_address'],
                    'payment_method' => $validated['payment_method'] ?? null,
                    'status' => 'pending',
                    'order_date' => now()
                ];
                
                // Only set admin_id if request is from authenticated admin
                if ($isAdmin) {
                    $orderData['admin_id'] = $request->user()->id;
                }
                
                $order = Order::create($orderData);

                // Create order items
                foreach ($orderItemsData as $itemData) {
                    $order->orderItems()->create($itemData);
                }

                $order->load(['orderItems.product']);
                if ($isAdmin) {
                    $order->load('admin');
                }
                
                return response()->json($order, Response::HTTP_CREATED);
            });
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create order',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     * Only accessible by authenticated admins.
     */
    public function show(Request $request, string $id)
    {
        try {
            // Verify user is admin
            if (!$request->user() instanceof \App\Models\Admin) {
                return response()->json([
                    'error' => 'Access denied. Admin privileges required.'
                ], Response::HTTP_FORBIDDEN);
            }

            $order = Order::with(['admin', 'orderItems.product'])->findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Order not found',
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     * Only accessible by authenticated admins.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Verify user is admin
            if (!$request->user() instanceof \App\Models\Admin) {
                return response()->json([
                    'error' => 'Access denied. Admin privileges required.'
                ], Response::HTTP_FORBIDDEN);
            }

            $order = Order::findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'sometimes|required|string|in:pending,processing,shipped,delivered,cancelled',
                'customer_name' => 'sometimes|required|string|max:255',
                'customer_email' => 'sometimes|required|email|max:255',
                'customer_phone' => 'sometimes|nullable|string|max:20',
                'shipping_address' => 'sometimes|required|string',
                'payment_method' => 'sometimes|nullable|string'
            ]);

            $order->update($validated);
            $order->load(['admin', 'orderItems.product']);

            return response()->json($order);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update order',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Only accessible by authenticated admins.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            // Verify user is admin
            if (!$request->user() instanceof \App\Models\Admin) {
                return response()->json([
                    'error' => 'Access denied. Admin privileges required.'
                ], Response::HTTP_FORBIDDEN);
            }

            $order = Order::findOrFail($id);
            
            // Only allow deletion of pending or cancelled orders
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                return response()->json([
                    'error' => 'Cannot delete order with status: ' . $order->status
                ], Response::HTTP_FORBIDDEN);
            }
            
            $order->delete();

            return response()->json([
                'message' => 'Order deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete order',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
