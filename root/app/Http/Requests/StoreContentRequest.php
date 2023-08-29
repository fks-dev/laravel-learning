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
            'content_type'       => 'required | integer',
            'is_public'          => 'required',
            'movie_file_path'    => 'mimes:mp4,avi,webm | max:2048', //2MBまで
            'document_file_path' => 'mimes:xls,xlsx,doc,docx,pdf,ppt,pptx,txt', //Excel、Word、PDF、パワポ、テキスト
        ];
        if ($this->input('content_type') === 6) {
            $rules['time_limit_minutes'] = 'integer | min:1 | max:100';
            $rules['passing_score_rate'] = 'integer | min:1 | max:100';
            $rules['amount_questions']   = 'integer | min:1 | max:100';
        }

        return $rules;
    }
}
