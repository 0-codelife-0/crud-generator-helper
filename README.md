# Helper package for Laravel

## Installation

You can install this plugin into your Laravel application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```shell
composer require codelife/codelife-model-generator-helper
```

If above code does not work try: 
```shell
composer require codelife/codelife-model-generator-helper:dev-main
#  OR
composer require codelife/codelife-model-generator-helper:dev-master
```

## This helper requires this plugin to work
```shell
composer require doctrine/dbal
```


### Load this in confgi/app.php

```php
...
Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider::class,
...
```

### Publish commands with the below command
```shell
php artisan vendor:publish --provider="Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider" --tag="commands"
```

### Publish assets with the below command
```shell
php artisan vendor:publish --provider="Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider" --tag="commands"
```

## Usage

### Create view 
```
php artisan create:crud-view `ModelName/TableName`

// Options
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
```

### Create controller 
```
php artisan create:crud-controller `ModelName/TableName`

// Options
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
```

### Create route 
```
php artisan create:crud-route `ModelName/TableName`

// Options
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
```

### Create model 
```
php artisan create:model `ModelName/TableName`

// Options
(-O "Table1/Table2," | Table1/Table2) | (--hasOne="Table1/Table2" )
(-B "Table1/Table2," | Table1/Table2) | (--belongsTo="Table1/Table2" )
(-M "Table1/Table2," | Table1/Table2) | (--hasMany="Table1/Table2" )
```

