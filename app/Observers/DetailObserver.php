<?php

namespace App\Observers;


use App\Models\Detail;

class DetailObserver
{
    public function created(Detail $content)
    {
        $content->afterCreated();
    }

}
