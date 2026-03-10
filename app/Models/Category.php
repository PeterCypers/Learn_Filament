<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    // works opositely to fillable, fillable states which attributes can be mass assigned,
    // while guarded states what can't be mass assigned, an empty array means everything is mass-assignable
    // protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
