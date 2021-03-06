<?php

namespace App\Services\Purchase;

use App\Purchase;
use App\Services\Deliverable\DeliverableRequestReader;
use Illuminate\Http\Request;

class PurchaseRequestReader extends DeliverableRequestReader
{

    public function readAttributes(Request $request, $type)
    {
        switch ($type) {
            case Purchase::class:
                return $this->readDeliverableAttributes($request);
            default:
                return parent::readAttributes($request, $type);
        }
    }

}
