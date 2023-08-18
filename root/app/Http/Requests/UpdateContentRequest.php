<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContentRequest extends FormRequest
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
            'course_id'=> 'required',
            'title'    => 'required',
            'kind'     => 'required',
            'public'   => 'required',
            'movie'    => 'mimes:mp4,avi,webm | max:2048', //2MBまで
        ];
        if ($this->input('kind') === 'テスト') {
            $rules['testTime'] = 'integer | min:1 | max:100';
            $rules['testPer'] = 'integer | min:1 | max:100';
            $rules['testVol'] = 'integer | min:1 | max:100';
        }

        return $rules;
    }
}
