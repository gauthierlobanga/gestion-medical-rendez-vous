<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Nnjeim\World\Models\Country;
// use Nnjeim\World\Models\City;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{
    // use SoftDeletes;

    // protected $fillable = [
    //     'firstname',
    //     'lastname',
    //     'email',
    //     'phone',
    //     'country_id', 
    //     'city_id',
    //     'subject_id',
    //     'message'
    // ];

    // public function country()
    // {
    //     return $this->belongsTo(Country::class);
    // }

    // public function city()
    // {
    //     return $this->belongsTo(City::class);
    // }

    // public function subject()
    // {
    //     return $this->belongsTo(Subject::class);
    // }
}
