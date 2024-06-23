<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassRequest extends FormRequest
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
    public function rules(): array
    {
        return [
			'class_name' => 'required|unique:classes',
			'course_id' => 'required',
			'teacher_id' => 'required',
			'schedule_from' => 'required',
			'exclude_dates' => 'array',
			// 'exclude_dates' => 'string',
        ];
    }
}
