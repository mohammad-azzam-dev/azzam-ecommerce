<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderDetailAddon extends Model
{
    protected $guarded = [];

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }
}
