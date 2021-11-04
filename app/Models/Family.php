<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'style_price',
        'family_price',
        'axes',
        'filename',
        'sample_text',
    ];

    protected $casts = [
        'axes' => 'json',
    ];

    const PATH = 'families';

    protected function getFullPath()
    {
        $paths = [self::PATH, $this->filename];
        $path = implode(DIRECTORY_SEPARATOR, $paths);
        return storage_path($path);
    }
}
