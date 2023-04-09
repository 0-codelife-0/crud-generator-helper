<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider;

class CreateModel extends Command
{
    // Use shortcut -B User or -B "User"
    protected $signature = 'create:model {name} {--M|hasMany=} {--O|hasOne=} {--B|belongsTo=}';
    protected $description = 'Create a model with fillables and relations flags';
    protected $files;

    public function __construct(Filesystem $files){
        parent::__construct();
        $this->files = $files;
    }

    public function handle(){
        if(DB::connection()->getPdo()){
            if($this->getColumnsList($this->argument('name')) != null){
                $path = $this->getSourceFilePath();
                $this->makeDirectory(dirname($path));
                $contents = $this->getSourceFile();
                if (!$this->files->exists($path)) {
                    $this->files->put($path, $contents);
                    $this->info("File : {$path} created");
                }else{
                    $this->error("File : {$path} already exists");
                }
            }else{
                $this->error("Failed to generate model, either table does not exists or table has empty fields.");
            }
        }else{
            $this->error('Could not connect to database!' . $e);
        }
    }

    public function getSingularClassName($name){
        return ucwords(Pluralizer::singular($name));
    }

    public function getStubPath(){
        return ModelHelperServiceProvider::STUB_PATH.'create-model.stub';
    }

    public function getStubVariables(){
        return [
            'NAMESPACE'         => 'App\\Models',
            'CLASS_NAME'        => $this->getSingularClassName($this->argument('name')),
            'CONTENT'           => $this->generateFillableField($this->getColumnsList($this->argument('name'))),
            'RELATIONS'         => $this->getRelations($this->option('hasMany'), $this->option('hasOne'), $this->option('belongsTo'))
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
        return base_path('App\\Models') .'\\' .$this->getSingularClassName($this->argument('name')) . '.php';
    }

    protected function makeDirectory($path){
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    protected function generateFillableField($columns){
        $fillables = 'protected $fillable = [';
        foreach ($columns as $key => $value) {
            if(($value == end($columns)) && (!in_array($value, ['id', 'created_at', 'updated_at', 'deleted_at']))){
                $fillables .= "\n\t";
                $fillables .= "'".$value."'". ',';
            }else if(!in_array($value, ['id', 'created_at', 'updated_at', 'deleted_at'])){
                $fillables .= "\n\t\t";
                $fillables .= "'".$value."'". ',';
            }
        }
        $fillables .= "\n\t".'];';
        return $fillables;
    }

    // requires to run composer require doctrine/dbal
    protected function getColumnsList($name){
        $columns = DB::getSchemaBuilder()->getColumnListing(Str::snake(Pluralizer::plural($name)));
        return $columns;
    }

    protected function getRelations($hasMany, $hasOne, $belongsTo){
        $relations = '';
        if($hasMany != null){
            foreach(explode("/", $hasMany) as $relation){
                if($relation != ''){
                    $relations .= "\t".'public function '.Str::camel(Pluralizer::plural($relation)).'(){
        return $this->hasMany('.Str::studly(Pluralizer::singular($relation)).'::class);
    }'."\n\n";
                }
            }
        }

        if($hasOne != null){
            foreach(explode("/", $hasOne) as $relation){
                if($relation != ''){
                    $relations .= "\t".'public function '.Str::camel(Pluralizer::singular($relation)).'(){
        return $this->hasOne('.Str::studly(Pluralizer::singular($relation)).'::class);
    }'."\n\n";
                }
            }
        }

        if($belongsTo != null){
            foreach(explode("/", $belongsTo) as $relation){
                if($relation != ''){
                    $relations .= "\t".'public function '.Str::camel(Pluralizer::singular($relation)).'(){
        return $this->belongsTo('.Str::studly(Pluralizer::singular($relation)).'::class);
    }'."\n\n";
                }

            }
        }
        return $relations;
    }
}
