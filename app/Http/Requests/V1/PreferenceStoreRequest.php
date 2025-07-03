<?php

declare(strict_types=1);

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class PreferenceStoreRequest extends FormRequest
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
            'sources' => ['sometimes', 'required', 'array'],
            'sources.*' => ['required_with:sources', 'exists:sources,name'],
            'authors' => ['sometimes', 'required', 'array'],
            'authors.*' => ['required_with:authors', 'exists:authors,name'],
            'categories' => ['sometimes', 'required', 'array'],
            'categories.*' => ['required_with:categories', 'exists:categories,name'],
        ];
    }
}
