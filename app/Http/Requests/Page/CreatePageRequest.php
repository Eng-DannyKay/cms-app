<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }

 

public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'slug' => [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            Rule::unique('pages')->where(function ($query) {
                return $query->where('client_id', auth()->user()->client->id);
            })
        ],
        'is_published' => 'boolean',
        'content' => 'sometimes|array',
        'content.sections' => 'sometimes|array',
    ];
}

    public function prepareForValidation()
    {
        if (!$this->has('slug') && $this->has('title')) {
            $this->merge(['slug' => \Illuminate\Support\Str::slug($this->title)]);
        }
    }
}
