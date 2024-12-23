<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static upsert(array $dataToInsert, string[] $array, string[] $array1)
 * @method static max(string $string)
 * @method static where(string $string, string $string1)
 */
class Currency extends Model
{
    const CURRENCY_LIST_UPDATE_FREQUENCY_IN_SECONDS = 300;
    public $timestamps = false;
}
