<?php

use App\Livewire\Users\Index as UserIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('/users', UserIndex::class)->name('users.index');
});
