<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestGroupController;
use App\Http\Controllers\TestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| USERS → Admin only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])

    ->group(function () {

        Route::get('/', function () {
            return 'Admin Dashboard';
        })->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('test-categories', TestCategoryController::class);
        Route::resource('test-groups', TestGroupController::class);
        Route::resource('tests', TestController::class);

    });

/*
|--------------------------------------------------------------------------
| PATIENTS → Admin + Receptionist
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin,Receptionist'])
    ->group(function () {

        Route::resource('patients', PatientController::class);
    });

require __DIR__ . '/auth.php';