<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use App\Models\GuestOrder;
use App\Models\OrderDetail;
use App\Models\OrderMaster;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class OrderCompletionController extends Controller
{
    public function completeOrder(CheckoutRequest $request) {
        try {
            $carts = Cart::whereIn('id', $request->input('cartIds'))->get();

            $orderMaster = OrderMaster::create([
                'user_id' => $request->input('userId'),
                'guest_id' => $request->input('guestId'),
                'order_number' => null,
                'transaction_id' => $request->input('transactionId'),
                'total_price' => $carts->sum('price'),
                'payment_method' => 1,
                'purchase_date' => Carbon::now(),
                'payment_status' => 'paid'
            ]);
    
            if ($orderMaster) {
                $orderMaster->updateOrderNo();
            }

            foreach ($carts as $key => $cart) {
                OrderDetail::create([
                    'cart_id' => $cart->id,
                    'order_master_id' => $orderMaster->id,
                    'item_number' => '00' . ($key + 1),
                    'subtotal' => $cart->price,
                    'download_count' => 5,
                    'status' => 'completed'
                ]);
            }

            if ($request->input('guestId')) {
                GuestOrder::create([
                    'guest_id' => $request->input('guestId'),
                    'order_master_id' => $orderMaster->id,
                    'email_address' => $request->input('emailAddress'),
                    'first_name' => $request->input('firstName'),
                    'last_name' => $request->input('lastName')
                ]);
            }

            $carts->each(function ($cart) {
                $cart->update(['status' => 'sold']);
            });

            return response()->json([
                'message' => 'The order has been completed.',
                'orderNumber' => $orderMaster->order_number
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
