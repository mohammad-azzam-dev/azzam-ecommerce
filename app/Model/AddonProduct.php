<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AddonProduct extends Pivot
{
    protected $table = 'addon_product';
}
