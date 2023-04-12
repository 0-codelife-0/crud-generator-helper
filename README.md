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

## This helper requires this plugin to work properly
```shell
composer require doctrine/dbal
```


### Load this in config/app.php

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
php artisan vendor:publish --provider="Codelife\CodelifeModelGeneratorHelper\Providers\ModelHelperServiceProvider" --tag="assets"
```

## Usage

### Create view 
```shell
php artisan create:crud-view `ModelName/TableName`

...Options...
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
...If you want an Asynchronus Ajax And Json View, You can pass --ajaj flag...
(--ajaj)
```

### Create controller 
```shell
php artisan create:crud-controller `ModelName/TableName`

...Options...
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
...If you want an Asynchronus Ajax And Json Controller, You can pass --ajaj flag...
(--ajaj)
```

### Create route 
```shell
php artisan create:crud-route `ModelName/TableName`

...Options...
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
```

### Create model 
```shell
php artisan create:model `ModelName/TableName`

...Options...
(-O "Table1/Table2," | Table1/Table2) | (--hasOne="Table1/Table2" )
(-B "Table1/Table2," | Table1/Table2) | (--belongsTo="Table1/Table2" )
(-M "Table1/Table2," | Table1/Table2) | (--hasMany="Table1/Table2" )
```

### Create Model, View, Controller and Route at once
```shell
php artisan create:all `ModelName/TableName`

...Options...
(-P "Prefix\Name" | Prefix\Name) | (--prefix="Prefix\Name" )
(-O "Table1/Table2," | Table1/Table2) | (--hasOne="Table1/Table2" )
(-B "Table1/Table2," | Table1/Table2) | (--belongsTo="Table1/Table2" )
(-M "Table1/Table2," | Table1/Table2) | (--hasMany="Table1/Table2" )
...If you want an Asynchronus Ajax And Json Controller, You can pass --ajaj option...
(--ajaj)
```

## You can donate me in the following platforms to show your support and appreciation
Paypal Account: https://www.paypal.me/jexszcy
Gcash Account: 09218356618
