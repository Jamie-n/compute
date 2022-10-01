<?php

namespace App\Support\Enums;

enum Permissions: string
{
    case ADMIN = 'administrate';
    case MANAGE_PRODUCTS = 'manage_products';
    case MANAGE_USERS = 'manage_users';
    case MANAGE_SHIPPING = 'manage_shipping';
}
