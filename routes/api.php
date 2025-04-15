<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use DiegoDrese\NovaBackupManager\Http\Controllers\BackupController;
use DiegoDrese\NovaBackupManager\Http\Controllers\DownloadBackupController;
use DiegoDrese\NovaBackupManager\Http\Controllers\BackupStatusesController;
use DiegoDrese\NovaBackupManager\Http\Controllers\CleanBackupsController;
/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Route::get('/', function (Request $request) {
//     //
// });


Route::get('/backups', [BackupController::class, 'index']);
Route::post('/backups', [BackupController::class, 'store']);
Route::delete('/backups', [BackupController::class, 'destroy']);

Route::get('download-backup', DownloadBackupController::class);

Route::get('backup-statuses', BackupStatusesController::class.'@index');
Route::post('clean-backups', CleanBackupsController::class);
