<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_update_id',
        'uploaded_by',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'type',
        'description',
        'category',
        'is_public',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
    ];

    /**
     * Get the project this attachment belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the project update this attachment belongs to.
     */
    public function projectUpdate(): BelongsTo
    {
        return $this->belongsTo(ProjectUpdate::class);
    }

    /**
     * Get the user who uploaded this attachment.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL.
     */
    public function getUrlAttribute(): string
    {
        if ($this->is_public) {
            return Storage::disk('public')->url($this->file_path);
        }
        
        return route('attachments.download', $this->id);
    }

    /**
     * Get human readable file size.
     */
    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a document.
     */
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
        ]);
    }

    /**
     * Get file icon based on type.
     */
    public function getIconAttribute(): string
    {
        if ($this->isImage()) {
            return 'photo';
        }

        return match($this->mime_type) {
            'application/pdf' => 'description',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'article',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'table_chart',
            'text/plain' => 'text_snippet',
            default => 'attach_file',
        };
    }

    /**
     * Delete the file from storage when model is deleted.
     */
    protected static function booted(): void
    {
        static::deleting(function (ProjectAttachment $attachment) {
            if ($attachment->is_public) {
                Storage::disk('public')->delete($attachment->file_path);
            } else {
                Storage::disk('local')->delete($attachment->file_path);
            }
        });
    }
}
