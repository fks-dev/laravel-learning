<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Ramsey\Uuid\Type\Integer;

class SelectCourseRequest extends FormRequest
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
        return [
            'q_id' => ['integer'],
            'answer' => ['string', 'in:yes,no']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response("不正なパラメータが入力されています。", 400));
    }
}
