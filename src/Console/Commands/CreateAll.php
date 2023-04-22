<?php

namespace Codelife\CodelifeModelGeneratorHelper\Console\Commands;
// namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateAll extends Command
{
    protected $signature = 'create:all {name} {--P|prefix=} {--M|hasMany=} {--O|hasOne=} {--B|belongsTo=} {--ajaj}';
    protected $description = 'Command to run all crud generator command';

    public function handle()
    {
        $this->info('Creating Model...');
        Artisan::call('create:model', [
            'name' => $this->argument('name'),
            '--hasMany' => $this->option('hasMany'),
            '--hasOne' => $this->option('hasOne'),
            '--belongsTo' => $this->option('belongsTo'),
        ]);
        $this->line('<fg=black;bg=yellow>'.Artisan::output().'</>');
        $this->info('Creating Controller...');
        Artisan::call('create:crud-controller', [
            'name' => $this->argument('name'),
            '--prefix' => $this->option('prefix'),
            '--ajaj' => $this->option('ajaj')
        ]);
        $this->line('<fg=black;bg=yellow>'.Artisan::output().'</>');
        $this->info('Creating View...');
        Artisan::call('create:crud-view', [
            'name' => $this->argument('name'),
            '--prefix' => $this->option('prefix'),
            '--ajaj' => $this->option('ajaj')
        ]);
        $this->line('<fg=black;bg=yellow>'.Artisan::output().'</>');
        $this->info('Creating Route...');
        Artisan::call('create:crud-route', [
            'name' => $this->argument('name'),
            '--prefix' => $this->option('prefix'),
        ]);
        $this->line('<fg=black;bg=yellow>'.Artisan::output().'</>');
    }
}
