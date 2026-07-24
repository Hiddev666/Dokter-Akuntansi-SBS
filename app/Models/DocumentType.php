<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    #[Override]
    protected static function booted()
    {
        static::creating(function ($documentType) {
            $documentType->slug = Str::slug($documentType->name);
        });
    }
}
