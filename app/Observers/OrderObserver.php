<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    public function created(Order $content)
    {
        $content->afterCreated();
    }
}
