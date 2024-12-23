<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    const CURRENCY_LIST_UPDATE_FREQUENCY_IN_SECONDS = 300;
    public $timestamps = false;
}
