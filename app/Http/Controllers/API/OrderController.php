<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\States\Delivered;
use Illuminate\Http\Request;
use Spatie\ModelStates\Exceptions\CouldNotPerformTransition;

class OrderController extends Controller
{
    public function delivered(Request $request, Order $order)
    {
        try {
            $order->status->transitionTo(Delivered::class);
        } catch (CouldNotPerformTransition $e) {
            return response('', 403);
        }

        return response('', 200);
    }
}
