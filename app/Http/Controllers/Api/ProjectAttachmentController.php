<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectAttachmentController extends Controller
{
    /**
     * Store project attachments (photos/documents).
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,txt',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:500',
            'project_update_id' => 'nullable|exists:project_updates,id',
            'category' => 'nullable|string|in:project_update,general,document',
            'is_public' => 'nullable|boolean',
        ]);

        $uploadedFiles = [];
        $files = $request->file('files');
        $descriptions = $request->input('descriptions', []);
        $projectUpdateId = $request->input('project_update_id');
        $category = $request->input('category', 'general');
        $isPublic = $request->boolean('is_public', true);

        foreach ($files as $index => $file) {
            try {
                // Generate unique filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = Str::uuid() . '.' . $extension;
                
                // Store file in public disk
                $filePath = $file->storeAs('project-attachments/' . $project->id, $filename, 'public');
                
                // Create attachment record
                $attachment = ProjectAttachment::create([
                    'project_id' => $project->id,
                    'project_update_id' => $projectUpdateId,
                    'filename' => $filename,
                    'original_filename' => $originalName,
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'description' => $descriptions[$index] ?? null,
                    'category' => $category,
                    'is_public' => $isPublic,
                    'uploaded_by' => $request->user()?->id ?? 1, // Default to user ID 1 if no auth
                ]);

                $uploadedFiles[] = [
                    'id' => $attachment->id,
                    'filename' => $attachment->filename,
                    'original_filename' => $attachment->original_filename,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'description' => $attachment->description,
                    'download_url' => Storage::disk('public')->url($attachment->file_path),
                ];

            } catch (\Exception $e) {
                // If file upload fails, continue with other files
                continue;
            }
        }

        return response()->json([
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'data' => $uploadedFiles
        ], 201);
    }

    /**
     * Download attachment file.
     */
    public function download(ProjectAttachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_filename
        );
    }

    /**
     * Delete attachment.
     */
    public function destroy(ProjectAttachment $attachment): JsonResponse
    {
        // Delete file from storage
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        // Delete database record
        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully'
        ]);
    }

    /**
     * Get project attachments.
     */
    public function index(Project $project): JsonResponse
    {
        $attachments = $project->attachments()
            ->with('uploader:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'filename' => $attachment->filename,
                    'original_filename' => $attachment->original_filename,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'description' => $attachment->description,
                    'category' => $attachment->category,
                    'is_public' => $attachment->is_public,
                    'download_url' => Storage::disk('public')->url($attachment->file_path),
                    'uploaded_at' => $attachment->created_at->format('Y-m-d H:i:s'),
                    'uploader' => $attachment->uploader ? [
                        'id' => $attachment->uploader->id,
                        'name' => $attachment->uploader->name,
                    ] : null,
                ];
            });

        return response()->json([
            'data' => $attachments,
            'meta' => [
                'total' => $attachments->count(),
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ]
            ]
        ]);
    }
}
