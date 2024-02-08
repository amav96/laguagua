<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    use HasFactory;

    public $timestamps = false;
    
     /**
     * Table name.
     * @var string.
     */
    protected $table = 'paises';

   
}
