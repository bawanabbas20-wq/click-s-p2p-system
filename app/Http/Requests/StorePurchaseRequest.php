<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // We just check if the user is logged in.
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'bail' stops validation on first failure for cleaner error messages
            'item_name' => ['bail', 'required', 'string', 'max:255'],
            'estimated_price' => ['bail', 'required', 'numeric', 'min:0', 'max:999999999'], // Upper bound to prevent integer overflow
            'estimated_currency' => ['bail', 'required', 'string', 'in:IQD,USD'],
            'priority' => ['bail', 'required', 'string', 'in:low,medium,high'],
            'date_wanted' => ['bail', 'required', 'date', 'after_or_equal:today'],
            'justification' => ['bail', 'required', 'string', 'min:10', 'max:5000'], // Explicit max length
        ];
    }
}
