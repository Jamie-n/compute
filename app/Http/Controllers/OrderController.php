<?php

namespace App\Http\Controllers;

use App\Actions\Order\CreateOrder;
use App\Exceptions\InvalidOrderException;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Support\Cart\CartManager;
use App\Support\Enums\Alert;
use App\Support\PayPal\PaypalPaymentHandler;
use App\Support\States\Packing;
use Illuminate\Auth\Access\AuthorizationException;
use Throwable;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
            ->orders()
            ->latest()
            ->paginate(config('pagination.order_index_page_length'));

        return view('order.index')->with('orders', $orders);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('products')->with('totalCost');

        return view('order.show')->with('order', $order);
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Order $order)
    {
        $this->authorize('update', $order);

        return view('order.edit')->with('order', $order);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(Order $order, CheckoutRequest $request)
    {
        $this->authorize('update', $order);

        $order->deliveryAddress()->update($request->validated());

        session()->flash(Alert::SUCCESS->value, 'Shipping Information Updated Successfully.');
        return redirect()->route('order.index');
    }

    /**
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();
        app()->make(PaypalPaymentHandler::class)->voidAuthorisedPayment($order->paypal_transaction_id);

        session()->flash(Alert::SUCCESS->value, 'Order Cancelled Successfully');
        return redirect()->route('order.index');
    }
}
