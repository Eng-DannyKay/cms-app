<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'company_name' => $this->company_name,
            'slug' => $this->slug,
            'website_url' => $this->website_url,
            'logo' => $this->logo,
            'user' => new UserResource($this->whenLoaded('user')),
            'current_theme' => new ThemeResource($this->whenLoaded('currentTheme')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
