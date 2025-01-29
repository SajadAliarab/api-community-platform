<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];
    protected $casts = [
        'active' => 'boolean',
    ];
        // Relationship: Get parent category
        public function parent()
        {
            return $this->belongsTo(Category::class, 'parent_id');
        }
    
        // Relationship: Get child subcategories
        public function children()
        {
            return $this->hasMany(Category::class, 'parent_id');
        }
}
