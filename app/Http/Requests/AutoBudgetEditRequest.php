<?php

namespace App\Http\Requests;

use App\Models\AutoBudget;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AutoBudgetEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $budget = AutoBudget::where('user_id', Auth::id());
        return $budget !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "budget" => ["required"],
            "reset_date" => ["required", "numeric", "min:1", "max:31"],
            "currency" => ["required", "in:MKD,EUR,USD"]
        ];
    }
}
