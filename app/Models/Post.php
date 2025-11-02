<?php

namespace App\Models;

use App\Traits\Likeable;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{

    use HasFactory,
        SoftDeletes,
        Likeable,
        InteractsWithMedia,
        Searchable;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
        'slug',
        'published_at',
        'is_published',
        'views_count'
    ];

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('posts')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(1000)
            ->height(600)
            ->sharpen(10)
            ->performOnCollections('posts')
            ->format('webp')
            ->quality(80);

        $this->addMediaConversion('card')
            ->width(300)
            ->height(200)
            ->sharpen(10)
            ->performOnCollections('posts')
            ->format('webp')
            ->quality(80);
    }

    protected static function booted(): void
    {
        static::deleting(function ($post) {
            if ($post->isForceDeleting()) {
                $post->clearMediaCollection('posts');
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)
            ->using(PostTag::class)
            ->withPivot('created_at', 'updated_at', 'deleted_at')
            ->withTimestamps()
            ->wherePivotNull('deleted_at');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAvailable(Builder $builder): Builder
    {
        return $builder->where('is_published', true);
    }

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content'  => $this->content,
        ];
    }
}
