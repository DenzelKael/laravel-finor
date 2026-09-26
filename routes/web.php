<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        return view('settings.users.index');
        ;
    })->name('users.index');

    Route::get('/roles', function () {
        return view('settings.roles.index');
        ;
    })->name('roles.index');

    Route::get('/permissions', function () {
        return view('settings.permissions.index');
        ;
    })->name('permissions.index');
});