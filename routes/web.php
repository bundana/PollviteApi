<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Pollvite Url Shortener' => app()->version()];
});

