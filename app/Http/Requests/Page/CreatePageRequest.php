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
            'slug' => 'nullable|string|max:255|alpha_dash|unique:pages,slug,NULL,id,client_id,' . auth()->user()->client->id,
            'content' => 'required|array',
            'content.sections' => 'required|array|min:1',
            'content.sections.*.type' => 'required|string|in:hero,services,about,contact,features,testimonials',
            'is_published' => 'boolean'
        ];
    }

    public function prepareForValidation()
    {
        if (!$this->has('slug') && $this->has('title')) {
            $this->merge(['slug' => \Illuminate\Support\Str::slug($this->title)]);
        }
    }
}
