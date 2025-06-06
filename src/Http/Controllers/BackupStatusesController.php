<?php

namespace DiegoDrese\NovaBackupManager\Http\Controllers;

use DiegoDrese\NovaBackupManager\Support\BackupCache;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupStatusesController extends ApiController
{
    public function index(){
        return  BackupCache::backupStatuses(function () {
            $reflection = new \ReflectionMethod(BackupDestinationStatusFactory::class, 'createForMonitorConfig');
            $monitorBackupsType = $reflection->getParameters()[0]->getType()->getName();

            $monitorConfig =  $monitorBackupsType === 'Spatie\Backup\Config\MonitoredBackupsConfig'
                ? \Spatie\Backup\Config\MonitoredBackupsConfig::fromArray(config('backup.monitor_backups'))
                : config('backup.monitor_backups');

            return BackupDestinationStatusFactory::createForMonitorConfig($monitorConfig)
                ->map(function (BackupDestinationStatus $backupDestinationStatus) {
                    return [
                        'name' => $backupDestinationStatus->backupDestination()->backupName(),
                        'disk' => $backupDestinationStatus->backupDestination()->diskName(),
                        'reachable' => $backupDestinationStatus->backupDestination()->isReachable(),
                        'healthy' => $backupDestinationStatus->isHealthy(),
                        'amount' => $backupDestinationStatus->backupDestination()->backups()->count(),
                        'newest' => $backupDestinationStatus->backupDestination()->newestBackup()
                            ? $backupDestinationStatus->backupDestination()->newestBackup()->date()
                            : __('No backups present'),
                        'usedStorage' => Format::humanReadableSize($backupDestinationStatus->backupDestination()->usedStorage()),
                    ];
                })
                ->values()
                ->toArray();
        });
    }
}
