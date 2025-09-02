<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            "title" => "required|string|max:255",
            "description" => "string",
            "start_dateTime" => "required|date_format:Y-m-d H:i",
            "end_dateTime" => "required|date_format:Y-m-d H:i",
            "banner_link" => "string",
            "batches" => "required|nullable|array",
                "batches.*.price" => "required_with:batches.*| numeric|min:0",
                "batches.*.tickets_qty" => "required_with:batches.*| integer|min:1",
                "batches.*.end_dateTime" => "required_with:batches.*| date_format:Y-m-d H:i",
        ];
    }
}
