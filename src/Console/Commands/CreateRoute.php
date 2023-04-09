<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider;

class CreateRoute extends Command
{
    protected $signature = 'create:crud-route {name} {--P|prefix=}';
    protected $description = 'Create a route with initial crud operation route';

    public function __construct(){
        parent::__construct();
    }

    public function handle(){
        $path = $this->getSourceFilePath();
        $contents = $this->getSourceFile();
        if(strpos(file_get_contents($path), $contents) === false) {
            File::append($path, $contents);
            $this->info("Route added in: ". $path);
        }else{
            $this->error("Route already exists in: ". $path);
        }
    }

    public function getPluralModelName($name){
        return Str::studly(Pluralizer::plural($name));
    }

    public function getStubPath(){
        return ModelHelperServiceProvider::STUB_PATH.'create-route.stub';
    }

    public function getStubVariables(){
        return [
            'MODEL_CONTROLLER'   => $this->getPluralModelName($this->argument('name')),
            'MODEL_ROUTE'        => Str::kebab($this->getPluralModelName($this->argument('name'))),
            'PREFIX'             => ($this->option('prefix') != "" || $this->option('prefix') != null) ? '\\'.$this->option('prefix'):'',
            'ROUTE_PREFIX'       => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '/', strtolower($this->option('prefix'))).'/' :'',
            'ROUTE_NAME'         => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '.', strtolower($this->option('prefix'))).'.' :''
        ];
    }

    public function getSourceFile(){
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    public function getStubContents($stub , $stubVariables = []){
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace)
        {
            $contents = str_replace('$'.$search.'$' , $replace, $contents);
        }
        return $contents;
    }

    public function getSourceFilePath(){
        return base_path('routes/').'web.php';
    }
}
