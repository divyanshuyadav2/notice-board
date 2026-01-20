<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticeController;
// Redirect home to notices dashboard
Route::get('/', function () {
    return redirect()->route('notices.index');
});
Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');

Route::get('/notices/create', [NoticeController::class, 'create'])
    ->name('notices.create');

Route::post('/notices', [NoticeController::class, 'store'])
    ->name('notices.store');

Route::get('/notices/{notice}', [NoticeController::class, 'show'])
    ->name('notices.show');

Route::get('/notices/{notice}/edit', [NoticeController::class, 'edit'])
    ->name('notices.edit');

Route::post('/notices/{notice}/status', [NoticeController::class, 'updateStatus'])
    ->name('notices.updateStatus');
Route::put('/notices/{notice}', [NoticeController::class, 'update'])
    ->name('notices.update');
Route::get('/notices/publish/{notice}', [NoticeController::class, 'publish'])
    ->name('notices.publish');    
