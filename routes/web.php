<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestGroupController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\LabSampleController;
use App\Http\Controllers\LabResultController;


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

 Route::resource('users', UserController::class);

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
Route::middleware(['auth', 'role:Admin|Receptionist'])
    ->group(function () {

        Route::resource('patients', PatientController::class);
        Route::resource('lab-orders', LabOrderController::class);

      

// Sample management per order
Route::get('/lab-orders/{labOrder}/samples', [LabSampleController::class, 'index'])
    ->name('lab-orders.samples.index');

Route::get('/lab-orders/{labOrder}/samples/generate', [LabSampleController::class, 'create'])
    ->name('lab-orders.samples.create');

Route::post('/lab-orders/{labOrder}/samples/generate', [LabSampleController::class, 'store'])
    ->name('lab-orders.samples.store');

// Print label per sample 
Route::get('/lab-samples/{labSample}/label', [LabSampleController::class, 'label'])
    ->name('lab-samples.label');

        
    });



  

Route::middleware(['auth', 'role:Admin|Technician'])->group(function () {

    // Results screen for an order
    Route::get('/lab-orders/{labOrder}/results', [LabResultController::class, 'index'])
        ->name('lab-orders.results.index');

    // Save/update a single test result
    Route::patch('/lab-order-tests/{labOrderTest}/result', [LabResultController::class, 'updateResult'])
        ->name('lab-order-tests.result.update');

    // Verify a single test result
    Route::patch('/lab-order-tests/{labOrderTest}/verify', [LabResultController::class, 'verify'])
        ->name('lab-order-tests.verify');

});

require __DIR__ . '/auth.php';