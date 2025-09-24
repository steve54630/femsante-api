<?php

namespace App\Http\Request\Video;

use Illuminate\Foundation\Http\FormRequest;

class GenerateVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'video' => ['required', 'string']
        ];
    }
}