<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EarlyPaymentController extends Controller
{
    /**
     * Apply early payment discount for all orders of a customer in a specific voyage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'ship' => 'required|string',
            'voyage' => 'required|string',
            'customerId' => 'required|integer'
        ]);

        $shipNum = $request->ship;
        $voyageNum = $request->voyage;
        $customerId = $request->customerId;
        
        try {
            // Get the customer
            $customer = Customer::with('subAccounts')->findOrFail($customerId);
            
            // Get sub-account IDs
            $subAccountIds = $customer->subAccounts->pluck('sub_account_number')->toArray();
            
            // Check if customer is eligible for the discount
            $eligibleCustomerIds = [1001, 1002, 1003, 1004, 1005];
            if (!in_array($customerId, $eligibleCustomerIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not eligible for early payment discount'
                ], 400);
            }
            
            // Get all orders for this ship/voyage that belong to the customer
            $orders = Order::where('shipNum', $shipNum)
                ->whereRaw('voyageNum = ?', [$voyageNum])
                ->where(function($query) use ($customerId, $subAccountIds) {
                    $query->where(function($q) use ($customerId, $subAccountIds) {
                        $q->where('origin', 'Manila')
                            ->where(function($sq) use ($customerId, $subAccountIds) {
                                $sq->where('recId', $customerId)
                                    ->orWhereIn('recId', $subAccountIds);
                            });
                    })->orWhere(function($q) use ($customerId, $subAccountIds) {
                        $q->where('origin', 'Batanes')
                            ->where(function($sq) use ($customerId, $subAccountIds) {
                                $sq->where('shipperId', $customerId)
                                    ->orWhereIn('shipperId', $subAccountIds);
                            });
                    });
                })
                ->get();
            
            $updatedCount = 0;
            $totalDiscountAmount = 0;
            
            foreach ($orders as $order) {
                // Calculate 5% discount on freight
                $discountAmount = $order->freight * 0.05;
                $originalTotal = $order->totalAmount;
                
                // Apply the discount
                $order->discount = $discountAmount;
                $order->totalAmount = $originalTotal - $discountAmount;
                $order->early_payment_date = now(); // Mark the early payment date
                $order->save();
                
                $updatedCount++;
                $totalDiscountAmount += $discountAmount;
            }
            
            return response()->json([
                'success' => true,
                'message' => "Early payment discount applied successfully to {$updatedCount} orders",
                'discount_amount' => $totalDiscountAmount,
                'orders_affected' => $updatedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error applying early payment discount: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error applying early payment discount: ' . $e->getMessage()
            ], 500);
        }
    }
}
