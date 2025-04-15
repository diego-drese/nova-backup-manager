<?php

namespace DiegoDrese\NovaBackupManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;

class CreateRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return Config::get('nova-backup-manager.create');
    }
}









