<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSubject
 */
class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['description'];
}
