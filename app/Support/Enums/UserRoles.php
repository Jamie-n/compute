<?php

namespace App\Support\Enums;

enum UserRoles: string
{
    case PRODUCT_ADMIN = 'product_admin';
    case SYSTEM_ADMIN = 'system_admin';
    case WAREHOUSE_OPERATIVE = 'warehouse_operative';
}
