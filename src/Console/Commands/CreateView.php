<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider;

class CreateView extends Command
{
    protected $signature = 'create:crud-view {name} {--P|prefix=} {--ajaj}';
    protected $description = 'Create a view with initial crud operation view';
    protected $files;
    protected $numeric = ['integer', 'int', 'double', 'float', 'decimal', 'real', 'tinyint', 'smallint', 'mediumint', 'bigint'];
    protected $text = ['text', 'mediumtext', 'tinytext', 'longtext'];
    protected $dateTime = ['datetime', 'timestamp'];
    protected $time = 'time';
    protected $date = 'date';

    public function __construct(Filesystem $files){
        parent::__construct();
        $this->files = $files;
    }

    public function handle(){
        if(DB::connection()->getPdo()){
            if($this->getColumnsList($this->argument('name')) != null){
                if($this->option('ajaj')){
                    $indexPath = $this->getIndexSourceFilePath();
                    $appPath = $this->getAppSourceFilePath();
                    $this->makeDirectory(dirname($indexPath));
                    $this->makeDirectory(dirname($appPath));
                    $indexContents = $this->getIndexSourceFile();
                    $appContents = file_get_contents($this->getAppStubPath());
                    if (!$this->files->exists($indexPath)) {
                        $this->files->put($indexPath, $indexContents);
                        $this->info("File : {$indexPath} created");
                    }else{
                        $this->error("File : {$indexPath} already exists");
                    }
                    if(!$this->files->exists($appPath)){
                        $this->files->put($appPath, $appContents);
                        $this->info("File : {$appPath} created");
                    }else{
                        $this->error("File : {$appPath} already exists");
                    }
                }else{
                    $indexPath = $this->getIndexSourceFilePath();
                    $addPath = $this->getAddSourceFilePath();
                    $editPath = $this->getEditSourceFilePath();
                    $appPath = $this->getAppSourceFilePath();
                    $this->makeDirectory(dirname($indexPath));
                    $this->makeDirectory(dirname($addPath));
                    $this->makeDirectory(dirname($editPath));
                    $this->makeDirectory(dirname($appPath));
                    $indexContents = $this->getIndexSourceFile();
                    $addContents = $this->getAddSourceFile();
                    $editContents = $this->getEditSourceFile();
                    $appContents = file_get_contents($this->getAppStubPath());
                    if (!$this->files->exists($indexPath)) {
                        $this->files->put($indexPath, $indexContents);
                        $this->info("File : {$indexPath} created");
                    }else{
                        $this->error("File : {$indexPath} already exists");
                    }
                    if(!$this->files->exists($addPath)){
                        $this->files->put($addPath, $addContents);
                        $this->info("File : {$addPath} created");
                    }else{
                        $this->error("File : {$addPath} already exists");
                    }
                    if(!$this->files->exists($editPath)){
                        $this->files->put($editPath, $editContents);
                        $this->info("File : {$editPath} created");
                    }else{
                        $this->error("File : {$editPath} already exists");
                    }
                    if(!$this->files->exists($appPath)){
                        $this->files->put($appPath, $appContents);
                        $this->info("File : {$appPath} created");
                    }else{
                        $this->error("File : {$appPath} already exists");
                    }
                }
            }else{
                $this->error("Failed to generate view, either model's table does not exists or table has empty fields.");
            }
        }else{
            $this->error('Could not connect to database!' . $e);
        }
    }

    public function getPluralModelName($name){
        return Str::studly(Pluralizer::plural($name));
    }

    // get stub paths
    public function getIndexStubPath(){
        if($this->option('ajaj')){
            return ModelHelperServiceProvider::STUB_PATH.'create-ajax-view.stub';
        }else{
            return ModelHelperServiceProvider::STUB_PATH.'create-index.stub';
        }
    }
    public function getAddStubPath(){
        return ModelHelperServiceProvider::STUB_PATH.'create-add.stub';
    }
    public function getEditStubPath(){
        return ModelHelperServiceProvider::STUB_PATH.'create-edit.stub';
    }
    public function getAppStubPath(){
        return ModelHelperServiceProvider::STUB_PATH.'create-app.stub';
    }

    // get stub variables
    public function getIndexStubVariables(){
        return [
            'HEADER'       => $this->generateHeader($this->getColumnsList($this->argument('name'))),
            'BODY'         => $this->generateBody($this->getColumnsList($this->argument('name'))),
            'ROUTE'        => Str::kebab($this->getPluralModelName($this->argument('name'))),
            'PREFIX'       => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '.', strtolower($this->option('prefix')).'.') : '',
            'INPUTS'       => $this->getAddInputs($this->getColumnsList($this->argument('name'))),
            'JQUERY_INPUTS'=> $this->getJqueryInputs($this->getColumnsList($this->argument('name'))),
        ];
    }
    public function getAddStubVariables(){
        return [
            'MODEL_ROUTE'    => Str::kebab($this->getPluralModelName($this->argument('name'))),
            'INPUTS'         => $this->getAddInputs($this->getColumnsList($this->argument('name'))),
            'PREFIX'         => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '.', strtolower($this->option('prefix')).'.') : ''
        ];
    }
    public function getEditStubVariables(){
        return [
            'MODEL_ROUTE'    => Str::kebab($this->getPluralModelName($this->argument('name'))),
            'INPUTS'         => $this->getEditInputs($this->getColumnsList($this->argument('name'))),
            'PREFIX'         => ($this->option('prefix') != "" || $this->option('prefix') != null) ? str_replace('\\', '.', strtolower($this->option('prefix')).'.') : ''
        ];
    }

    // stub source file
    public function getIndexSourceFile(){
        return $this->getStubContents($this->getIndexStubPath(), $this->getIndexStubVariables());
    }
    public function getAddSourceFile(){
        return $this->getStubContents($this->getAddStubPath(), $this->getAddStubVariables());
    }
    public function getEditSourceFile(){
        return $this->getStubContents($this->getEditStubPath(), $this->getEditStubVariables());
    }

    public function getStubContents($stub , $stubVariables = []){
        $contents = file_get_contents($stub);
        foreach ($stubVariables as $search => $replace)
        {
            $contents = str_replace('$'.$search.'$' , $replace, $contents);
        }
        return $contents;
    }

    // Generate stub creating path
    public function getIndexSourceFilePath(){
        if($this->option('prefix') != "" || $this->option('prefix') != null){
            return base_path('resources/views/').str_replace('\\', '/', $this->option('prefix')).'/'.$this->getPluralModelName($this->argument('name')).'/index.blade.php';
        }else{
            return base_path('resources/views/').$this->getPluralModelName($this->argument('name')).'/index.blade.php';
        }
    }
    public function getEditSourceFilePath(){
        if($this->option('prefix') != "" || $this->option('prefix') != null){
            return base_path('resources/views/').str_replace('\\', '/', $this->option('prefix')).'/'.$this->getPluralModelName($this->argument('name')).'/edit.blade.php';
        }else{
            return base_path('resources/views/').$this->getPluralModelName($this->argument('name')).'/edit.blade.php';
        }
    }
    public function getAddSourceFilePath(){
        if($this->option('prefix') != "" || $this->option('prefix') != null){
            return base_path('resources/views/').str_replace('\\', '/', $this->option('prefix')).'/'.$this->getPluralModelName($this->argument('name')).'/add.blade.php';
        }else{
            return base_path('resources/views/').$this->getPluralModelName($this->argument('name')).'/add.blade.php';
        }
    }
    public function getAppSourceFilePath(){
        return base_path('resources/views/layout/').'app.blade.php';
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

    // Get inputs
    protected function getAddInputs($columns){
        $inputs = '';
        $database = Str::snake(Pluralizer::plural($this->argument('name')));
        foreach ($columns as $column) {
            $columnType = DB::getSchemaBuilder()->getColumnType($database, $column);
            if((!in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at']))){
                if(str_ends_with($column, '_id') != false){
                    $model =  Str::camel(Pluralizer::plural(str_replace('_id', '', $column)));
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<select name="'.$column.'" id="'.$column.'" class="form-control" required>
            <option value="">--Select '.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($model))))).'--</option>
            @foreach ($'.$model.' as $'.Pluralizer::singular($model).')
                <option value="{{ $'.Pluralizer::singular($model).'->id }}">{{ $'.Pluralizer::singular($model).'->id }}</option>
            @endforeach
        </select>'."\n\t";
                $inputs .= '</div>'."\n\t";
                }else if($column == 'password'){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="password" id="'.$column.'" name="'.$column.'" class="form-control" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if($columnType == 'date'){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="date" id="'.$column.'" name="'.$column.'" class="form-control" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if($columnType == 'time'){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="time" id="'.$column.'" name="'.$column.'" class="form-control" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if(in_array($columnType, $this->dateTime)){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="datetime-local" id="'.$column.'" name="'.$column.'" class="form-control" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if(in_array($columnType, $this->text)){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<textarea id="'.$column.'" name="'.$column.'" class="form-control" required></textarea>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if(in_array($columnType, $this->numeric)){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="number" id="'.$column.'" name="'.$column.'" class="form-control" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else{
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="text" id="'.$column.'" name="'.$column.'" class="form-control" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }
            }
        }
        return $inputs;
    }
    protected function getEditInputs($columns){
        $inputs = '';
        $database = Str::snake(Pluralizer::plural($this->argument('name')));
        foreach ($columns as $column) {
            $columnType = DB::getSchemaBuilder()->getColumnType($database, $column);
            if((!in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at']))){
                if(str_ends_with($column, '_id') != false){
                    $model =  Str::camel(Pluralizer::plural(str_replace('_id', '', $column)));
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<select name="'.$column.'" id="'.$column.'" class="form-control" required>
            <option value="">--Select '.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($model))))).'--</option>
            @foreach ($'.$model.' as $'.Pluralizer::singular($model).')
                <option value="{{ $'.Pluralizer::singular($model).'->id }}" {{ ($'.Pluralizer::singular($model).'->id == $data->'.$column.' ) ? "selected":"" }}>{{ $'.Pluralizer::singular($model).'->id }}</option>
            @endforeach
        </select>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if($column == 'password'){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="password" id="'.$column.'" name="'.$column.'" class="form-control" value="{{ $data->'.$column.' }}" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if($columnType == 'date'){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="date" id="'.$column.'" name="'.$column.'" class="form-control" value="{{ $data->'.$column.' }}" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if($columnType == 'time'){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="time" id="'.$column.'" name="'.$column.'" class="form-control" value="{{ $data->'.$column.' }}" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if(in_array($columnType, $this->dateTime)){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="datetime-local" id="'.$column.'" name="'.$column.'" class="form-control" value="{{ $data->'.$column.' }}" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if(in_array($columnType, $this->text)){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<textarea id="'.$column.'" name="'.$column.'" class="form-control" required>{{ $data->'.$column.' }}</textarea>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else if(in_array($columnType, $this->numeric)){
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="number" id="'.$column.'" name="'.$column.'" class="form-control" value="{{ $data->'.$column.' }}" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }else{
                    $inputs .= '<div class="form-group">'."\n";
                    $inputs .= "\t\t".'<label for="'.$column.'">'.Str::title(str_replace('_', ' ', (Str::snake(Pluralizer::singular($column))))).'</label>'."\n";
                    $inputs .= "\t\t".'<input type="text" id="'.$column.'" name="'.$column.'" class="form-control" value="{{ $data->'.$column.' }}" required>'."\n\t";
                    $inputs .= '</div>'."\n\t";
                }
            }
        }
        return $inputs;
    }
    protected function getJqueryInputs($columns){
        $inputs = '';
        $database = Str::snake(Pluralizer::plural($this->argument('name')));
        foreach($columns as $column){
            $columnType = DB::getSchemaBuilder()->getColumnType($database, $column);
            if((!in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at']))){
                $inputs .= '$("#'.$column.'").val(data.'.$column.');'."\n\t\t\t\t\t";
            }
        }
        return $inputs;
    }

    // Get header and body for index
    protected function generateHeader($columns){
        $data = '';
        foreach ($columns as $column) {
            $data .= "<th>{{ __('".$column."') }}</th>\n\t\t";
        }
        return $data;
    }
    protected function generateBody($columns){
        $data = '';
        foreach ($columns as $column) {
            if($this->option('ajaj')){
                $data .= "{data: '".$column."'},\n\t\t\t\t";
            }else{
                $data .= "<td>{{ \$data->".$column." }}</td>\n\t\t\t\t";
            }
        }
        return $data;
    }
}
