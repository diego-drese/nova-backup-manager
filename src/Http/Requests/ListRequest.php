<?php

namespace DiegoDrese\NovaBackupManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

class ListRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Config::get('nova-backup-manager.show_menu');
    }
    public function withValidator($validator) {
        $validator->after(function ($validator) {
            if(!in_array($this->disk, Config::get('backup.backup.destination.disks', []))){
                $validator->errors()->add('disk', 'This disk is not configured as a backup disk.');
            }
        });
    }
}









