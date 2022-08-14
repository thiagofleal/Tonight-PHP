# PHP Tonight framework
Tonight-PHP is a PHP mini-framework made to easily develop SQL Database connections, MVC structures and many other tools.

## Use
To use Tonight-PHP, just install via Composer:
```shell
composer require thiagofleal/tonight-php
```

### Examples

##### Database connection

```PHP
<?php

use Tonight\Data\DataBase;
use Tonight\Data\SQL;
use Tonight\Data\Drivers\MySQL;

$database = new DataBase(
  /* driver class */ MySQL::class,
  /* DSN */ "database=test;host=localhost",
  /* user */ "root",
  /* password */ ""
);

/* Load table "tbl_test" into $database (creates $database->tbl_test object) */
$database->load("tbl_test");

/* Insert row */
$database->tbl_test->insert([
  'cl_test1' => "First field",
  'cl_test2' => "Second field",
  'cl_test3' => "Third field"
]);
$database->tbl_test->commit();

/* Get all as Table */
$all = $database->tbl_test;

/* Get all as array */
$array = $database->tbl_test->get();

/* Select rows */
$selected = $database->tbl_test->where('id', SQL::EQUAL, 10)->toArrayList();

/* Update row */
$update = $selected->first();
$update->cl_test1 = "Field 1";
$database->tbl_test->setValue($update);
$database->tbl_test->commit();

/* Delete row */
$database->tbl_test->deleteWhere('id', SQL::EQUAL, 5);
$database->tbl_test->commit();
```

#### MVC
##### Config
```PHP
<?php

use Tonight\MVC\Config;

Config::setBaseUrl("localhost");
Config::setRoutesFolder("/example/");
Config::setModelsNamespace('Models');
Config::setControllersNamespace('Controllers');
Config::setViewsPath(__DIR__ . "/views");
Config::setTemplatesPath(__DIR__ . "/templates");
Config::setViewsExtension("php");
Config::setUrlGetter( function() {
  return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
});
```
##### Routes

```PHP
<?php
use Tonight\MVC\Config;

Config::setNotFoundRoute('ErrorController@notFound');
Config::addRoute('', 'ExampleController@index');
Config::addRoute('example/{value}', 'ExampleController@example');
```

##### Controller

```PHP
<?php

namespace Controllers;

use Tonight\MVC\Controller;

class ExampleController extends Controller {
  public function index($request, $args) {
    $this->setVariable("title", "Example");
    $this->render("page", "template");
  }
  
  public function example($request, $args) {
    $this->setVariable("value", $args->value);
    $this->render("example", "template");
  }
}
```
