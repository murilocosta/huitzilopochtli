<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

final class MerchandisePurchase extends Pivot
{

    const QUANTITY = 'quantity';
    const PURCHASE_PRICE = 'purchase_price';

    protected $fillable = [
        self::QUANTITY,
        self::PURCHASE_PRICE,
    ];

}
