<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectFileUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files' => 'required|array|max:10',
            'files.*' => [
                'required',
                'file',
                'max:102400', // 100MB max
                'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,csv,mp4,avi,mov,wmv,flv'
            ],
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'is_public' => 'boolean',
            'project_update_id' => 'nullable|exists:project_updates,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'files.required' => 'Please select at least one file to upload.',
            'files.*.required' => 'Each file is required.',
            'files.*.file' => 'Each upload must be a valid file.',
            'files.*.max' => 'Each file must not exceed 100MB.',
            'files.*.mimes' => 'File type not supported. Allowed types: images (jpg, png, gif), documents (pdf, doc, xls), videos (mp4, avi, mov).',
            'project_update_id.exists' => 'The selected project update does not exist.',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'files.*' => 'file',
            'descriptions.*' => 'description',
        ];
    }
}
