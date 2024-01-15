<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParadaItem extends Model
{
    use HasFactory;

    public $timestamps = false;
    
     /**
     * Table name.
     * @var string.
     */
    protected $table = 'paradas_items';

    /**
     * Table primary key.
     * @var string.
     */
    protected $primaryKey = 'id';

    /**
     * Table primary key autoincrementing?.
     * @var bool.
     */
    public $incrementing = true;

    /**
     * @var array.
     */
    protected $guarded = [];
}
