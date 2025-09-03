<?php

use Illuminate\Support\Facades\Route;

Route::get('/{r_st?}/{r_nd?}/{r_rd?}', function () {
    return view('app');
});

