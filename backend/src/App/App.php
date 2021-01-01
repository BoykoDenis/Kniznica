<?php
declare(strict_types=1);

namespace Enm\JsonApi\bswExample;

class App
{
	  public static $config;

	  public static $dbh;

    public static function __constructStatic()
    {
        $cfgPath = __DIR__."/../../config/config.inc.php";
        static::$config = include($cfgPath);

        static::connectDB( static::$config['DB']['DSN'],
                           static::$config['DB']['User'],
                           static::$config['DB']['Password'] );
    }

    public static function connectDB( $dsn, $user, $password )
    {
/*
        throw new \Exception('Debug: ' . static::$config['DB']['DSN']
                           . ';' . static::$config['DB']['User']
                           . ';' . static::$config['DB']['Password'] );
*/
        try {
            static::$dbh = new \PDO($dsn, $user, $password,
                                     array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
        } catch (\PDOException $e) {
            throw new \Exception('DB Connection failed: '. $e->getMessage());
        }
    }
}

App::__constructStatic();
