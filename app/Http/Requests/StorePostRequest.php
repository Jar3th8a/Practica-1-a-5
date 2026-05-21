<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:5|max:200|unique:posts',
            'content' => 'required|string|min:50',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array|min:1|max:5',
            'tags.*' => 'exists:tags,id',
            'published_at' => 'nullable|date|after:today',
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.min' => 'El título debe tener al menos 5 caracteres',
            'content.min' => 'El contenido debe tener al menos 50 caracteres',
            'tags.min' => 'Debes seleccionar al menos 1 etiqueta',
        ];
    }
}
