<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'question_id' => 'required',
            'content' => 'required|max:1600000',
        ];
    }

    public function messages()
    {
        return [
            'content.required' => trans('messages.content_required'),
            'content.max' => trans('messages.content_max'),
        ];
    }
}
