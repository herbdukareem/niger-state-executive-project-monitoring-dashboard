<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectFileUploadRequest;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectUpdate;
use App\Services\FileUploadService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProjectAttachmentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Upload files to a project.
     */
    public function store(ProjectFileUploadRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $projectUpdate = null;
        if ($request->filled('project_update_id')) {
            $projectUpdate = ProjectUpdate::findOrFail($request->project_update_id);
        }

        $attachments = $this->fileUploadService->uploadMultipleFiles(
            $request->file('files'),
            $project,
            $projectUpdate,
            $request->get('descriptions', []),
            $request->get('category', ''),
            $request->boolean('is_public')
        );

        return response()->json([
            'message' => 'Files uploaded successfully.',
            'attachments' => $attachments,
        ]);
    }

    /**
     * Download a file.
     */
    public function download(ProjectAttachment $attachment)
    {
        $this->authorize('view', $attachment);

        return $this->fileUploadService->downloadFile($attachment);
    }

    /**
     * Delete a file.
     */
    public function destroy(ProjectAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        $success = $this->fileUploadService->deleteFile($attachment);

        if ($success) {
            return response()->json(['message' => 'File deleted successfully.']);
        }

        return response()->json(['message' => 'Failed to delete file.'], 500);
    }

    /**
     * Update file details.
     */
    public function update(Request $request, ProjectAttachment $attachment)
    {
        $this->authorize('update', $attachment);

        $validated = $request->validate([
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'is_public' => 'boolean',
        ]);

        $attachment->update($validated);

        return response()->json([
            'message' => 'File updated successfully.',
            'attachment' => $attachment,
        ]);
    }
}
