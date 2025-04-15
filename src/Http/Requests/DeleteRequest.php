<?php

namespace DiegoDrese\NovaBackupManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

class DeleteRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Config::get('nova-backup-manager.delete');
    }
    public function withValidator($validator) {
        $validator->after(function ($validator) {

            $validDisks = config('backup.backup.destination.disks', []);
            if (!in_array($this->disk, $validDisks)) {
                $validator->errors()->add('option', 'Disk is not a valid backup disk.');
            }

            if (!str_ends_with($this->path, '.zip')) {
                $validator->errors()->add('path', 'File is not a zip file.');
            }

            if (str_contains($this->path, '..')) {
                $validator->errors()->add('path', 'Invalid backup path.');
            }
        });
    }
}









