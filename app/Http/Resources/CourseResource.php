<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'location' => [
                'city' => $this->city,
                'province' => $this->province,
                'coordinates' => [
                    'lat' => $this->latitude,
                    'lng' => $this->longitude,
                ],
                'distance_km' => isset($this->distance) ? round((float) $this->distance, 1) : null,
            ],
            'course' => [
                'holes' => $this->holes,
                'par' => $this->par,
                'year_opened' => $this->year_opened,
                'designer' => $this->designer,
            ],
            'description' => $this->description,
            'status' => $this->status,
            'review_count' => $this->whenCounted('reviews'),
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
