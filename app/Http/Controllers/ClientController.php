<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Get authenticated client's profile
     */
    public function getProfile(): JsonResponse
    {
        $client = auth()->user()->client;
        
        return response()->json([
            'data' => [
                'id' => $client->id,
                'company_name' => $client->company_name,
                'slug' => $client->slug,
                'website_url' => $client->website_url,
                'logo' => $client->logo ? Storage::url($client->logo) : null,
                'logo_path' => $client->logo,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
                'user' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ]
            ]
        ]);
    }

    /**
     * Update client profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $client = auth()->user()->client;

        $validator = Validator::make($request->all(), [
            'company_name' => 'sometimes|required|string|max:255',
            'website_url' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // If company name changes, update the slug
        if (isset($data['company_name']) && $data['company_name'] !== $client->company_name) {
            $data['slug'] = $this->generateUniqueSlug($data['company_name'], $client->id);
        }

        $client->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $client->fresh()
        ]);
    }

    /**
     * Upload client logo
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $client = auth()->user()->client;

        // Delete old logo if exists
        if ($client->logo) {
            Storage::delete($client->logo);
        }

        // Store new logo
        $path = $request->file('logo')->store("clients/{$client->id}/logos", 'public');
        
        $client->update(['logo' => $path]);

        return response()->json([
            'message' => 'Logo uploaded successfully',
            'data' => [
                'logo_url' => Storage::url($path),
                'logo_path' => $path
            ]
        ]);
    }

    /**
     * Delete client logo
     */
    public function deleteLogo(): JsonResponse
    {
        $client = auth()->user()->client;

        if ($client->logo) {
            Storage::delete($client->logo);
            $client->update(['logo' => null]);

            return response()->json([
                'message' => 'Logo deleted successfully'
            ]);
        }

        return response()->json([
            'message' => 'No logo to delete'
        ], 404);
    }

    /**
     * Generate unique slug for client
     */
    private function generateUniqueSlug(string $companyName, int $clientId): string
    {
        $slug = \Illuminate\Support\Str::slug($companyName);
        $originalSlug = $slug;
        $counter = 1;

        while (Client::where('slug', $slug)->where('id', '!=', $clientId)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Get client statistics
     */
    public function getStats(): JsonResponse
    {
        $client = auth()->user()->client;

        $stats = [
            'total_pages' => $client->pages()->count(),
            'published_pages' => $client->pages()->where('is_published', true)->count(),
            'total_views' => $client->pages()->withCount('views')->get()->sum('views_count'),
            'average_views' => $client->pages()->withCount('views')->get()->avg('views_count'),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}