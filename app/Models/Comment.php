<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\Likeable;

class Comment extends Model
{
    use HasFactory, Likeable, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'guest_name',
        'guest_email',
        'avatar_bg',
        'body',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function booted()
    {

        static::creating(function ($comment) {
            if (!$comment->avatar_bg) {
                $comment->avatar_bg = '#' . substr(md5($comment->guest_name), 0, 6);
            }
        });
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->withDefault([
                'name' => $this->guest_name,
                'profile_photo_url' => $this->guest_avatar
            ]);
    }

    public function guestAvatar(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->generateAvatar(),
        );
    }

    private function generateAvatar()
    {
        $name = $this->guest_name ?: 'Anonymous';

        // Nouvelle logique d'initiales
        $initials = collect(explode(' ', $name))
            ->filter()
            ->take(2)
            ->map(fn($word) => mb_substr($word, 0, 1, 'UTF-8'))
            ->join('');

        return sprintf(
            '<svg class="w-6 h-6" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" rx="50" fill="%s"/>
                <text x="50%%" y="50%%" dominant-baseline="middle" text-anchor="middle" 
                      fill="white" font-size="40" font-family="Arial">%s</text>
            </svg>',
            $this->avatar_bg,
            Str::upper($initials)
        );
    }

    public function scopeRootComments($query)
    {
        return $query->whereNull('parent_id')
            ->with(['replies' => function ($query) {
                $query->withDepth()->latest();
            }]);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies');
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }
}
