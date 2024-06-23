<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserEditRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        $rules = [
            'email' => 'required|email|unique:users,id,'.$this->route('user')->id ?? '',
			'name' => 'required',
            'permissions'=>'array'
        ];
        
        $password = $request->get('password', null);

        if(!is_null($password) && $password != ''){
            $rules['password'] = [
                'string',
                'min:6'
            ];
        }

        return $rules;
    }
}
