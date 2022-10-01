<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Support\Enums\Permissions;
use App\Support\Enums\UserRoles;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Order $order): bool
    {
        return $order->user->is($user) || $user->hasRole(UserRoles::SYSTEM_ADMIN->value);
    }

    public function update(User $user, Order $order): Response
    {
        if (!$order->canEditOrder())
            return Response::deny('You cannot edit an order which has begun shipping');

        if (!$order->user->is($user) && !$user->hasPermissionTo(Permissions::ADMIN->value))
            return Response::deny();

        return Response::allow();
    }

    public function delete(User $user, Order $order): Response
    {
        if (!$order->canEditOrder())
            return Response::deny('You cannot edit an order which has begun shipping');

        if (!$order->user->is($user) || !$order->paypal_transaction_id)
            return Response::deny();

        return Response::allow();
    }
}
