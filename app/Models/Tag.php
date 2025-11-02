<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(Post::class)
            ->using(PostTag::class)
            ->withPivot('created_at', 'updated_at', 'deleted_at')
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }
}
