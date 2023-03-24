<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    protected $fillable = ['name', 'price', 'is_active'];

    public function products()
    {
        return $this->belongsToMany(Product::class)->using(AddonProduct::class);
    }
}
