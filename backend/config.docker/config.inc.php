<?
/*
 * Docker version of configuration file
 * It's draft version. The configuration is hardcoded for now. See ../../.env for detail.
 */

return [
    "DB" => [
/*
        "DSN" => "mysql:host=localhost;dbname=mydb",
        "User" => "mysqluser",
        "Password" => "mysqlpswd",
*/
        "DSN" => "mysql:host=db.srv;dbname=kniznica",
        "User" => "kniznica",
        "Password" => "MySQL",

    ],
    "View" => [
        "RowsOnPage" => 5,
    ],
    "URI" => [
        "PageNoParam" => 'number',
        "PageSizeParam" => 'size',
    ],
];
