<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DeliveryCompanyProvince extends Model
{
    protected $fillable = ['delivery_company_id', 'province_id'];

    public $timestamps = false;
}
