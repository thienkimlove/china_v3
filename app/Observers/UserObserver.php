<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    public function created(User $content)
    {
        $content->afterCreated();
    }
}
