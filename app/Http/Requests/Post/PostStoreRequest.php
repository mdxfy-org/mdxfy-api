<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'as' => 'required|in:post,draft',
            'content' => 'required|string|min:20|max:100000',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'answer_to' => 'nullable|exists:pgsql.post.post,uuid',
            'visibility' => 'required|in:public,private,friends',
        ];
    }
}
