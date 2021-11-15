<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreSecretRequest extends FormRequest
{
  protected $redirect=false;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
  protected function failedValidation(Validator $validator) {
    throw new HttpResponseException(response()->preferredFormat($validator->errors(), 422));
  }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'secret'=>'string|unique:secrets|required',
            'remainingViews'=>'required|integer|gt:0',
            'expireAfter'=>'integer|required'
        ];
    }
}
