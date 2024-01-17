<?php 
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

function beginTransaction(){
    DB::beginTransaction();
}

function rollBack(){
    DB::rollBack();
}

function commit(){
    DB::commit();
}

function getNumbers(string $value){
    return preg_replace('/[^0-9]/', '', $value);
}

function setTimestampFieldDB(string $value){
    return Carbon::parse($value);
}