<?php
namespace DiegoDrese\NovaBackupManager\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BackupCache
{
    public static function backups(string $disk, \Closure $callback) {
        $key = self::keyForDisk($disk);
        return Cache::remember($key, now()->addHour(), $callback);
    }

    public static function backupStatuses(\Closure $callback){
        return Cache::remember(self::statusKey(), now()->addHour(), $callback);
    }

    public static function forgetBackupsConfig(): void{
        $disks =  Config::get('backup.backup.destination.disks', []);
        foreach($disks as $disk){
            Cache::forget(self::keyForDisk($disk));
        }
    }
    public static function forgetBackups(string $disk): void{
        Cache::forget(self::keyForDisk($disk));
    }

    public static function forgetStatuses(): void{
        Cache::forget(self::statusKey());
    }

    public static function keyForDisk(string $disk): string{
        return "backups-{$disk}";
    }

    public static function statusKey(): string{
        return "backup-statuses";
    }
}
