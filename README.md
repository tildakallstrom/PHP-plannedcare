# Planned care
In this repo I am fetching info from the insurance fund
statistics for "Number of people who have received planned care abroad".

## PHP
The info is fetched from an API: https://www.forsakringskassan.se/fk_apps/MEKAREST/public/v1/iv-planerad/
IVplaneradvardland.json, and put in a database.

### Config.php
The config file is ignored when pushing. To get the code working add the following in a config.php file:
`
spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.class.php';
});

define("DBHOST", "");
define("DBUSER", "");
define("DBPASS", "");
define("DBDATABASE", "");

error_reporting(-1);
ini_set("display_errors", 1);
`