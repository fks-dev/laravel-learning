<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'course_id'        => 'required|integer|between:190001,199999',
            'admin_id'         => 'required|integer|between:120001,129999',
            'title'            => 'required|string|max:255',
            'youtube_video_id' => 'required|string|max:255|regex:/^\S*$/',
            'remarks'          => 'max:500'
        ];

        return $rules;
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'admin_id'           => '管理者ID',
            'course_id'          => '所属コース',
            'title'              => 'コンテンツ名',
            'youtube_video_id'   => 'YouTube',
            'remarks'            => '備考'
        ];
    }
}
