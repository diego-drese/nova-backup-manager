<?php

namespace DiegoDrese\NovaBackupManager;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaBackupManager extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot(): void {
        Nova::mix('nova-backup-manager', __DIR__.'/../dist/mix-manifest.json');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     */
    public function menu(Request $request): ?MenuSection {
        if (!config('nova-backup-manager.show_menu', true)) {
            return null;
        }
        return MenuSection::make(__('Backups'))
            ->path('/backup-manager')
            ->icon('server');
    }
}
