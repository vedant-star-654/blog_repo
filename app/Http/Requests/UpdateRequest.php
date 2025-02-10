<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Ensure the user is logged in
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
            'user_id' => 'required|exists:users,id', // Ensure the user_id exists in the users table
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'title.required'   => 'The post title is required.',
            'content.required' => 'The post content is required.',
            'image.image'      => 'The uploaded file must be an image.',
            'image.mimes'      => 'Only JPEG, PNG, JPG, and GIF formats are allowed.',
            'image.max'        => 'Image size should not exceed 2MB.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists'   => 'Invalid user selected.',
        ];
    }
}
