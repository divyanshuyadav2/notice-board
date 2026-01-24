<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ShareControlller;

// Redirect home to notices dashboard
// routes/web.php
Route::middleware(['resolve.user'])->group(function () {
    Route::get('/select-organization', [OrganizationController::class, 'index'])
        ->name('organization.select');
    
    // Store should NOT have ensure.org middleware
    Route::get('/store-organization', [OrganizationController::class, 'store'])
        ->name('organization.store');
});

Route::middleware(['resolve.user', 'ensure.org'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('notices.index');
    });
    
    Route::get('/notices', [NoticeController::class, 'index'])->name('notices.index');
});

 Route::middleware(['resolve.user','ensure.org'])->group(function () {




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
 });
Route::get('/session-expired', function () {
    return response()
        ->view('errors.sessionexpires', [], 401);
})->name('session.expired');
Route::post('/notices/{id}/share', [ShareControlller::class, 'createShareLink'])
    ->name('notices.share.create');

Route::get('/share/{token}', [ShareControlller::class, 'viewSharedNotice']) 
    ->name('notices.share.view');







