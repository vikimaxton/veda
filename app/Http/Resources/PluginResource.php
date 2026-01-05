<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PluginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'version' => $this->version,
            'description' => $this->description,
            'author' => $this->author,
            'is_active' => $this->is_active,
            'is_installed' => $this->is_installed,
            'permissions' => $this->getPermissions(),
            'hooks' => array_keys($this->getHooks()),
            'blocks' => $this->getBlocks(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
