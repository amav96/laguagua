<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActivarActualizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:activar-actualizacion';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indica que hay una actualizacion de la aplicacion';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('usuarios')->update([
            "actualizacion" => 'https://play.google.com/apps/internaltest/4701325107361853033',
            "version"   => env("NUEVA_VERSION")   
        ]);
    }
}
