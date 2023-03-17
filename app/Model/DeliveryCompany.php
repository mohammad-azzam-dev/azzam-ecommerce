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

    public function countryProvinces()
    {
        return $this->hasManyThrough(
            CountryProvince::class,
            DeliveryCompanyProvince::class,
            'delivery_company_id', // Foreign key on the DeliveryCompanyProvince table
            'id', // Local key on the CountryProvince table
            'id', // Local key on the DeliveryCompany table
            'province_id' // Foreign key on the CountryProvince table
        );
    }
}
