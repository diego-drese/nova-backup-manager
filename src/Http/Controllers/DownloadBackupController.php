<?php

namespace  DiegoDrese\NovaBackupManager\Http\Controllers;

use DiegoDrese\NovaBackupManager\Http\Requests\DownloadRequest;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Symfony\Component\HttpFoundation\Response;

class DownloadBackupController extends ApiController {
    public function __invoke(DownloadRequest $request) {
        $backupDestination = BackupDestination::create($request->get('disk'), config('backup.backup.name'));
        $backup = $backupDestination->backups()->first(function (Backup $backup) use ($request) {
            return $backup->path() === $request->get('path');
        });
        if (!$backup) {
            return response(__('Backup not found'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->respondWithBackupDownload($backup, $request->get('disk'));
    }

    public function respondWithBackupDownload(Backup $backup, string $disk): Response {
        $path = $backup->path();
        $fileName = pathinfo($path, PATHINFO_BASENAME);

        $storage = Storage::disk($disk);
        $driver = $storage->getDriver();
        $isCloudWithTemporaryUrl = method_exists($storage, 'temporaryUrl');
        if ($isCloudWithTemporaryUrl) {
            try {
                $url = $storage->temporaryUrl($path, now()->addMinutes(5));
                return redirect()->away($url);
            } catch (\Throwable $e) {
                return response("Unable to generate temporary download link.", 500);
            }
        }

        if ($storage->exists($path)) {
            return $storage->download($path, $fileName);
        }

        return response("Backup not found on the given disk.", Response::HTTP_NOT_FOUND);
    }


}
