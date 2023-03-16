<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliveryCompany extends Model
{
    protected $fillable = ['name', 'phone_number'];

    public function provinces()
    {
        return $this->hasMany(DeliveryCompanyProvince::class);
    }
}
