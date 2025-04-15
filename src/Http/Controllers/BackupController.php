<?php

namespace DiegoDrese\NovaBackupManager\Http\Controllers;

use DiegoDrese\NovaBackupManager\Http\Requests\CreateRequest;
use DiegoDrese\NovaBackupManager\Http\Requests\DeleteRequest;
use DiegoDrese\NovaBackupManager\Http\Requests\ListRequest;
use DiegoDrese\NovaBackupManager\Jobs\CreateBackupJob;
use DiegoDrese\NovaBackupManager\Support\BackupCache;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Helpers\Format;

class BackupController extends ApiController {
    public function index(ListRequest $request){
        $backupDestination = BackupDestination::create($request->disk, config('backup.backup.name'));
        return BackupCache::backups($request->disk, function () use ($backupDestination) {
            return $backupDestination->backups()
                ->map(function (Backup $backup) {
                    $size = method_exists($backup, 'sizeInBytes') ? $backup->sizeInBytes() : $backup->size();
                    return [
                        'path' => $backup->path(),
                        'date' => $backup->date(),
                        'size' => Format::humanReadableSize($size),
                    ];
                })
                ->toArray();
        });
    }

    public function store(CreateRequest $request){
        $option = $request->input('option', '');
        dispatch(new CreateBackupJob($option))->onQueue(config('nova-backup-manager.queue'));
    }

    public function delete(DeleteRequest $request){
        $backupDestination = BackupDestination::create($request->get('disk'), config('backup.backup.name'));
        $backupDestination
            ->backups()
            ->first(function (Backup $backup) use ($request) {
                return $backup->path() === $request->get('path');
            })
            ->delete();
        BackupCache::forgetBackups($request->get('disk'));
        BackupCache::forgetStatuses();
        $this->respondSuccess();
    }
}
