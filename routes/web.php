<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticeController;
// Redirect home to notices dashboard
Route::get('/', function () {
    return redirect()->route('notices.index');
});

// Notices dashboard (list)

// Create notice form
Route::get('/notices/create', function () {
    return view('notices.create');
})->name('notices.create');


Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
Route::get('/notices/create', [NoticeController::class, 'create'])->name('notices.create');
Route::post('/notices', [NoticeController::class, 'store'])->name('notices.store');
Route::get('/notices/{notice}', [NoticeController::class, 'show'])->name('notices.show');
Route::post('/notices/{notice}/status', [NoticeController::class, 'updateStatus'])->name('notices.updateStatus');

