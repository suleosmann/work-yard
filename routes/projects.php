<?php

use App\Livewire\Projects\Index as ProjectIndex;
use App\Livewire\Resources\ResourceList;
use App\Livewire\Tasks\Board;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('projects')->name('projects.')->group(function () {
    Route::livewire('/', ProjectIndex::class)->name('index');
    Route::livewire('/{project}', Board::class)->name('board');
    Route::livewire('/{project}/resources', ResourceList::class)->name('resources');
});
