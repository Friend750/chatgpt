<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}', function ($user, $id) {
    return dd((int) $user->id === (int) $id); // السماح فقط للمستقبل بالاشتراك في القناة
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return dd((int) $user->id === (int) $id);
});
