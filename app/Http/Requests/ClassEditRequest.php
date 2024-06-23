<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassEditRequest extends FormRequest
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
            'class_name' => 'required|unique:classes,class_name,'.$this->route('class')->id ?? '',
			'course_id' => 'required',
			'teacher_id' => 'required',
			'schedule_from' => 'required',
			'exclude_dates' => 'array',
			'status' => [Rule::in([-1, 1, 0])],
        ];
    }
}
