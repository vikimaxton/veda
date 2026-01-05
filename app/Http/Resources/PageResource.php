<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'template' => $this->template,
            'content_schema' => $this->content_schema,
            'seo_meta' => $this->seo_meta,
            'parent_id' => $this->parent_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relationships
            'creator' => new UserResource($this->whenLoaded('creator')),
            'parent' => new PageResource($this->whenLoaded('parent')),
            'children' => PageResource::collection($this->whenLoaded('children')),
            'versions_count' => $this->when(
                $this->relationLoaded('versions'),
                fn() => $this->versions->count()
            ),
            
            // Computed properties
            'is_published' => $this->isPublished(),
            'has_children' => $this->hasChildren(),
            'url' => '/' . $this->slug,
        ];
    }
}
