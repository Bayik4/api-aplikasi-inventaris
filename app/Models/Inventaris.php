<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'code',
        'stuf_name',
        'category',
        'amount',
        'condition',
        'purchase_date',
        'information'
    ];
}
