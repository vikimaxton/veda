<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $pageId = $this->route('page')->id ?? null;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('cms_pages', 'slug')->ignore($pageId),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'
            ],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'private'])],
            'template' => ['sometimes', 'string', 'max:255'],
            'content_schema' => ['sometimes', 'required', 'array'],
            'content_schema.*.type' => ['required_with:content_schema', 'string'],
            'content_schema.*.attributes' => ['nullable', 'array'],
            'seo_meta' => ['sometimes', 'array'],
            'seo_meta.title' => ['nullable', 'string', 'max:255'],
            'seo_meta.description' => ['nullable', 'string', 'max:500'],
            'seo_meta.og_image' => ['nullable', 'url'],
            'parent_id' => ['sometimes', 'nullable', 'uuid', 'exists:cms_pages,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Page title is required.',
            'slug.regex' => 'Slug must be lowercase with hyphens only.',
            'slug.unique' => 'This slug is already in use.',
            'content_schema.required' => 'Page content is required.',
            'content_schema.*.type.required' => 'Each content block must have a type.',
            'parent_id.exists' => 'The selected parent page does not exist.',
        ];
    }
}
