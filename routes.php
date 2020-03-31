<?php 

use Illuminate\Support\Facades\Route;


Route::get('change-locale/{locale}', function($locale) {
    \Session::put('locale', $locale);
    return back();
});