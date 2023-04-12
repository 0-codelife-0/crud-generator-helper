<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider;

class CreateController extends Command
{
    // Usage with prefix php artisan create:crud-controller Todo --prefix="Admin\User"
    protected $signature = 'create:crud-controller {name} {--P|prefix=} {--ajaj}';
    protected $description = 'Create a controller with initial crud operation logic';
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
                $this->error("Failed to generate crud controller, either table does not exists or table has empty fields.");
            }
        }else{
            $this->error('Could not connect to database!' . $e);
        }
    }

    public function getSingularClassName($name){
        return ucwords(Pluralizer::singular($name));
    }

    public function getPluralModelName($name){
        return Str::studly(Pluralizer::plural($name));
    }

    public function getStubPath(){
        if($this->option('ajaj')){
            return ModelHelperServiceProvider::STUB_PATH.'create-ajax-controller.stub';
        }else{
            return ModelHelperServiceProvider::STUB_PATH.'create-controller.stub';
        }
    }

    public function getStubVariables(){
        return [
            'NAMESPACE'         => ($this->option('prefix') != "" || $this->option('prefix') != null) ? 'App\\Http\\Controllers\\'.$this->option('prefix'):'App\\Http\\Controllers',
            'CLASS_NAME'        => $this->getPluralModelName($this->argument('name')),
            'MODEL'             => $this->getSingularClassName($this->argument('name')),
            'VIEW_FOLDER'       => $this->getPluralModelName($this->argument('name')),
            'ROUTE'             => Str::kebab($this->getPluralModelName($this->argument('name'))),
            'PREFIX'            => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '.', $this->option('prefix').'.') : '',
            'PREFIX_ROUTE'      => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '.', strtolower($this->option('prefix')).'.') : '',
            'CONTROLLER'        => ($this->option('prefix') != "" || $this->option('prefix') != null) ? "\n".'use App\Http\Controllers\Controller;':'',
            'RELATION_MODEL'    => $this->getRelationModel($this->getColumnsList($this->argument('name'))),
            'RELATION_QUERY'    => $this->getRelationQuery($this->getColumnsList($this->argument('name'))),
            'ADD_RELATION'      => $this->getAddRelationVariable($this->getColumnsList($this->argument('name'))),
            'EDIT_RELATION'     => $this->getEditRelationVariable($this->getColumnsList($this->argument('name')))
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
        if($this->option('prefix') != "" || $this->option('prefix') != null){
            return base_path('App\\Http\\Controllers\\'.$this->option('prefix')) .'\\' .$this->getPluralModelName($this->argument('name')) . 'Controller.php';
        }else{
            return base_path('App\\Http\\Controllers') .'\\' .$this->getPluralModelName($this->argument('name')) . 'Controller.php';
        }
    }

    protected function makeDirectory($path){
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    protected function getColumnsList($name){
        $columns = DB::getSchemaBuilder()->getColumnListing(Str::snake(Pluralizer::plural($name)));
        return $columns;
    }

    protected function getRelationModel($columns){
        $relation = '';
        foreach ($columns as $column) {
            if(str_ends_with($column, '_id') != false){
                $model = str_replace('_id', '', $column);
                $relation .= "\n".'use App\Models\\'.Str::studly(Pluralizer::singular($model)).';';
            }
        }
        return $relation;
    }

    protected function getAddRelationVariable($columns){
        $relation = ', [';
        foreach ($columns as $column) {
            if(str_ends_with($column, '_id') != false){
                $model = str_replace('_id', '', $column);
                $relation .= "\n\t\t\t"."'".Str::camel(Pluralizer::plural($model))."' "."=> "."$".Str::camel(Pluralizer::plural($model)).",";
            }
        }
        $relation .= "\n\t\t".']';
        return $relation;
    }
    protected function getEditRelationVariable($columns){
        $relation = ',';
        foreach ($columns as $column) {
            if(str_ends_with($column, '_id') != false){
                $model = str_replace('_id', '', $column);
                $relation .= "\n\t\t\t"."'".Str::camel(Pluralizer::plural($model))."' "."=> "."$".Str::camel(Pluralizer::plural($model)).",";
            }
        }
        return $relation;
    }

    protected function getRelationQuery($columns){
        $relation = '';
        foreach ($columns as $column) {
            if(str_ends_with($column, '_id') != false){
                $model = str_replace('_id', '', $column);
                $relation .= "\n\t\t"."$".Str::camel(Pluralizer::plural($model))." = ".Str::studly(Pluralizer::singular($model))."::get();";
            }
        }
        return $relation;
    }
}
