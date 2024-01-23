<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentRequest extends FormRequest
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
            'course_id'          => 'required',
            'title'              => 'required',
            'youtube_video_id'   => 'required',
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
            'course_id'          => '所属コース',
            'title'              => 'コンテンツ名',
            'youtube_video_id'   => 'YouTube'
        ];
    }
}
