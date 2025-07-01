<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prefix',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // Get all categories ordered by default first, then alphabetically
    public static function getAllCategories()
    {
        return self::orderBy('is_default', 'desc')
                  ->orderBy('name', 'asc')
                  ->pluck('name')
                  ->toArray();
    }

    // Get category prefix by name
    public static function getPrefixByName($name)
    {
        $category = self::where('name', $name)->first();
        return $category ? $category->prefix : null;
    }

    // Get all category prefixes as key-value pairs
    public static function getAllPrefixes()
    {
        return self::pluck('prefix', 'name')->toArray();
    }
}
