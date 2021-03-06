<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Delivery extends Model
{

    use SoftDeletes;
    
    const SENT_AT = 'sent_at';
    const ARRIVED_AT = 'arrived_at';
    const DELIVERY_TIME = 'delivery_time';

    const DELIVERABLE_ID = 'deliverable_id';
    const DELIVERABLE_TYPE = 'deliverable_type';
    
    const RELATIONSHIP_DELIVERY_ADDRESS = 'address';
    
    protected $fillable = [
        self::SENT_AT,
        self::ARRIVED_AT,
        self::DELIVERY_TIME,
    ];

    protected $hidden = [
        self::DELIVERABLE_ID,
        self::DELIVERABLE_TYPE,
    ];
    
    public function address()
    {
        return $this->belongsTo(DeliveryAddress::class);
    }

    public function createAddress(DeliveryAddress $deliveryAddress)
    {
        $this->address()->associate($deliveryAddress);
    }
    
}
