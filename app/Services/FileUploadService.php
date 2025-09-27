<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectUpdate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    /**
     * Allowed file types for uploads.
     */
    const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'];
    const ALLOWED_VIDEO_TYPES = ['mp4', 'avi', 'mov', 'wmv', 'flv'];

    /**
     * Maximum file sizes in bytes.
     */
    const MAX_IMAGE_SIZE = 10 * 1024 * 1024; // 10MB
    const MAX_DOCUMENT_SIZE = 50 * 1024 * 1024; // 50MB
    const MAX_VIDEO_SIZE = 100 * 1024 * 1024; // 100MB

    /**
     * Upload a file for a project.
     */
    public function uploadProjectFile(
        UploadedFile $file,
        Project $project,
        ?ProjectUpdate $projectUpdate = null,
        string $description = '',
        string $category = '',
        bool $isPublic = false
    ): ProjectAttachment {
        $this->validateFile($file);

        $fileType = $this->determineFileType($file);
        $disk = $isPublic ? 'project_files' : 'project_documents';
        
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file, $project);
        
        // Store the file
        $path = $file->storeAs(
            $this->getStoragePath($project, $fileType),
            $filename,
            $disk
        );

        // Create thumbnail for images
        $thumbnailPath = null;
        if ($fileType === 'photo' && $isPublic) {
            $thumbnailPath = $this->createThumbnail($file, $project, $filename);
        }

        // Create attachment record
        return ProjectAttachment::create([
            'project_id' => $project->id,
            'project_update_id' => $projectUpdate?->id,
            'uploaded_by' => auth()->id(),
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'type' => $fileType,
            'description' => $description,
            'category' => $category,
            'is_public' => $isPublic,
        ]);
    }

    /**
     * Upload multiple files.
     */
    public function uploadMultipleFiles(
        array $files,
        Project $project,
        ?ProjectUpdate $projectUpdate = null,
        array $descriptions = [],
        string $category = '',
        bool $isPublic = false
    ): array {
        $attachments = [];

        foreach ($files as $index => $file) {
            $description = $descriptions[$index] ?? '';
            $attachments[] = $this->uploadProjectFile(
                $file,
                $project,
                $projectUpdate,
                $description,
                $category,
                $isPublic
            );
        }

        return $attachments;
    }

    /**
     * Validate uploaded file.
     */
    private function validateFile(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $size = $file->getSize();

        // Check if file type is allowed
        $allowedTypes = array_merge(
            self::ALLOWED_IMAGE_TYPES,
            self::ALLOWED_DOCUMENT_TYPES,
            self::ALLOWED_VIDEO_TYPES
        );

        if (!in_array($extension, $allowedTypes)) {
            throw new \InvalidArgumentException("File type '{$extension}' is not allowed.");
        }

        // Check file size based on type
        if (in_array($extension, self::ALLOWED_IMAGE_TYPES) && $size > self::MAX_IMAGE_SIZE) {
            throw new \InvalidArgumentException('Image file size exceeds maximum allowed size of 10MB.');
        }

        if (in_array($extension, self::ALLOWED_DOCUMENT_TYPES) && $size > self::MAX_DOCUMENT_SIZE) {
            throw new \InvalidArgumentException('Document file size exceeds maximum allowed size of 50MB.');
        }

        if (in_array($extension, self::ALLOWED_VIDEO_TYPES) && $size > self::MAX_VIDEO_SIZE) {
            throw new \InvalidArgumentException('Video file size exceeds maximum allowed size of 100MB.');
        }
    }

    /**
     * Determine file type based on extension.
     */
    private function determineFileType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, self::ALLOWED_IMAGE_TYPES)) {
            return 'photo';
        }

        if (in_array($extension, self::ALLOWED_DOCUMENT_TYPES)) {
            return 'document';
        }

        if (in_array($extension, self::ALLOWED_VIDEO_TYPES)) {
            return 'video';
        }

        return 'other';
    }

    /**
     * Generate unique filename.
     */
    private function generateUniqueFilename(UploadedFile $file, Project $project): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "project_{$project->id}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get storage path for file.
     */
    private function getStoragePath(Project $project, string $fileType): string
    {
        return "{$project->id}/{$fileType}s";
    }

    /**
     * Create thumbnail for image.
     */
    private function createThumbnail(UploadedFile $file, Project $project, string $filename): ?string
    {
        try {
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = $this->getStoragePath($project, 'photo') . '/thumbnails/' . $thumbnailFilename;
            
            $image = Image::make($file->getRealPath())
                ->resize(300, 300, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            Storage::disk('project_files')->put($thumbnailPath, $image->encode());
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            // Log error but don't fail the upload
            \Log::error('Failed to create thumbnail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete file and its attachment record.
     */
    public function deleteFile(ProjectAttachment $attachment): bool
    {
        try {
            $disk = $attachment->is_public ? 'project_files' : 'project_documents';
            
            // Delete main file
            Storage::disk($disk)->delete($attachment->file_path);
            
            // Delete thumbnail if exists
            if ($attachment->type === 'photo' && $attachment->is_public) {
                $thumbnailPath = str_replace('/photos/', '/photos/thumbnails/thumb_', $attachment->file_path);
                Storage::disk($disk)->delete($thumbnailPath);
            }
            
            // Delete attachment record
            $attachment->delete();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file download response.
     */
    public function downloadFile(ProjectAttachment $attachment)
    {
        $disk = $attachment->is_public ? 'project_files' : 'project_documents';
        
        if (!Storage::disk($disk)->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }
        
        return Storage::disk($disk)->download(
            $attachment->file_path,
            $attachment->original_filename
        );
    }
}
