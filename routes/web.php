<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\TestCategoryController;
use App\Http\Controllers\TestGroupController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\LabSampleController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\LabReportController;

use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffNotificationController;




Route::middleware('auth')->group(function () {
    Route::get('/staff-notifications/unread', [StaffNotificationController::class, 'unread'])->name('staff-notifications.unread');
    Route::post('/staff-notifications/{staffNotification}/read', [StaffNotificationController::class, 'markRead'])->name('staff-notifications.read');
    Route::post('/staff-notifications/read-all', [StaffNotificationController::class, 'markAllRead'])->name('staff-notifications.read-all');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

});

/*
|--------------------------------------------------------------------------
| USERS → Admin only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])

    ->group(function () {

       
        Route::resource('users', UserController::class);
        Route::resource('test-categories', TestCategoryController::class);
        Route::resource('test-groups', TestGroupController::class);
        Route::resource('tests', TestController::class);
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

      

    });

/*
|--------------------------------------------------------------------------
| Lab Orders – read (Admin, Receptionist, Lab Technician)
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Lab Orders – write + Patients + Notifications (Admin, Receptionist)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin|Receptionist'])->group(function () {

    // Patient scan must be defined before the resource so 'scan' isn't captured as {patient}
    Route::get('/patients/scan',  [PatientController::class, 'scanForm'])->name('patients.scan');
    Route::post('/patients/scan', [PatientController::class, 'scan'])->name('patients.scan.submit');
    Route::get('/patients/{patient}/label', [PatientController::class, 'label'])->name('patients.label');

    Route::resource('patients', PatientController::class);

    // create must come before {labOrder} show route to avoid 'create' being captured as an ID
    Route::get('/lab-orders/create', [LabOrderController::class, 'create'])->name('lab-orders.create');
    Route::post('/lab-orders', [LabOrderController::class, 'store'])->name('lab-orders.store');
    Route::get('/lab-orders/{labOrder}/edit', [LabOrderController::class, 'edit'])->name('lab-orders.edit');
    Route::put('/lab-orders/{labOrder}', [LabOrderController::class, 'update'])->name('lab-orders.update');
    Route::patch('/lab-orders/{labOrder}', [LabOrderController::class, 'update']);
    Route::delete('/lab-orders/{labOrder}', [LabOrderController::class, 'destroy'])->name('lab-orders.destroy');
    Route::patch('/lab-orders/{labOrder}/cancel', [LabOrderController::class, 'cancel'])->name('lab-orders.cancel');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/retry', [NotificationController::class, 'retry'])->name('notifications.retry');

});

/*
|--------------------------------------------------------------------------
| Lab Orders – read (Admin, Receptionist, Lab Technician)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin|Receptionist|Lab Technician'])->group(function () {
    Route::get('/lab-orders', [LabOrderController::class, 'index'])->name('lab-orders.index');
    Route::get('/lab-orders/{labOrder}', [LabOrderController::class, 'show'])->name('lab-orders.show');
});

/*
|--------------------------------------------------------------------------
| Samples + Reports (Admin, Receptionist, Lab Technician)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin|Receptionist|Lab Technician'])->group(function () {

    Route::get('/lab-orders/{labOrder}/samples', [LabSampleController::class, 'index'])
        ->name('lab-orders.samples.index');
    Route::get('/lab-orders/{labOrder}/samples/generate', [LabSampleController::class, 'create'])
        ->name('lab-orders.samples.create');
    Route::post('/lab-orders/{labOrder}/samples/generate', [LabSampleController::class, 'store'])
        ->name('lab-orders.samples.store');
    Route::get('/lab-samples/{labSample}/label', [LabSampleController::class, 'label'])
        ->name('lab-samples.label');
    Route::get('/lab-orders/{labOrder}/samples/print-all', [LabSampleController::class, 'printAll'])
        ->name('lab-orders.samples.print-all');

    Route::get('/lab-orders/{labOrder}/report', [LabReportController::class, 'show'])
        ->name('lab-reports.show');
    Route::get('/lab-orders/{labOrder}/report/pdf', [LabReportController::class, 'downloadPdf'])
        ->name('lab-reports.pdf');
    Route::get('/reports/access/{token}', [PublicReportController::class, 'show'])
        ->name('public-reports.show');

});

// Results – view only (Admin, Lab Technician, Receptionist)
Route::middleware(['auth', 'role:Admin|Lab Technician|Receptionist'])->group(function () {

    Route::get('/lab-orders/{labOrder}/results', [LabResultController::class, 'index'])
        ->name('lab-orders.results.index');

});

// Sample scan – Admin + Lab Technician
Route::middleware(['auth', 'role:Admin|Lab Technician'])->group(function () {
    Route::get('/scan', [LabSampleController::class, 'scanForm'])->name('samples.scan');
    Route::post('/scan', [LabSampleController::class, 'scan'])->name('samples.scan.submit');
});

// Results – write actions (Admin, Lab Technician only)
Route::middleware(['auth', 'role:Admin|Lab Technician'])->group(function () {





    Route::patch('/lab-orders/{labOrder}/results', [LabResultController::class, 'bulkUpdateResults'])
        ->name('lab-results.bulk-update');

    Route::patch('/lab-orders/{labOrder}/results/verify', [LabResultController::class, 'bulkVerify'])
        ->name('lab-results.bulk-verify');

    Route::patch('/lab-orders/{labOrder}/approve', [LabOrderController::class, 'approve'])
        ->name('lab-orders.approve');

    Route::patch('/lab-orders/{labOrder}/hold', [LabOrderController::class, 'hold'])
        ->name('lab-orders.hold');

    // // Save/update a single test result
    // Route::patch('/lab-order-tests/{labOrderTest}/result', [LabResultController::class, 'updateResult'])
    //     ->name('lab-order-tests.result.update');

    // // Verify a single test result
    // Route::patch('/lab-order-tests/{labOrderTest}/verify', [LabResultController::class, 'verify'])
    //     ->name('lab-order-tests.verify');

});

require __DIR__ . '/auth.php';