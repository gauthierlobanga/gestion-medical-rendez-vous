<?php

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Likeable
{

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function like(User $user): void
    {
        if(!$this->isLikedBy($user)) 
        {
            $this->likes()->create(['user_id' => $user->id]);
        }
    }

    public function unlike(User $user): void
    {
        $this->likes()->where('user_id', $user->id)->delete();
    }

    public function toggleLike(User $user): void
    {
        $this->isLikedBy($user) ? $this->unlike($user) : $this->like($user);
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function likesCount(): int
    {
        return $this->likes()->count();
    }
}