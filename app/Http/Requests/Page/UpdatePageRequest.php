<?php

namespace App\Http\Requests\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }

   public function rules(): array
{
    $pageId = $this->route('page');

    return [
        'title' => 'sometimes|string|max:255',
        'slug' => [
            'sometimes',
            'string',
            'max:255',
            'alpha_dash',
            Rule::unique('pages')->where(function ($query) use ($pageId) {
                return $query->where('client_id', auth()->user()->client->id)
                            ->where('id', '!=', $pageId);
            })
        ],
        'is_published' => 'boolean',
        'content' => 'sometimes|array',
        'content.sections' => 'required|array|min:1',
    ];
}
}
