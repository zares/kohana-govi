<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Illuminate\Database for Kohana Framework
 *
 * @package    Govi
 * @category   Kohana-module
 * @author     S.Zares <sergiozares@gmail.com>
 * @copyright  (c) 2013 S.Zares
 * @license    MIT License
 *
 */
abstract class Govi_Fluent {

    /**
     * Database config group.
     *
     * @var string
     */
    protected static $group = 'default';

    /**
     * Database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected static $connection;

    /**
    * Dynamically pass methods to the default connection.
    *
    * @param  string  $method
    * @param  array   $parameters
    * @return mixed
    */
    public static function __callStatic($method, $parameters)
    {
        if ( ! isset(static::$connection))
        {
            static::getConnection();
        }

        return call_user_func_array(array(static::$connection, $method), $parameters);
    }

    /**
     * Get a database connection instance.
     *
     * @return Illuminate\Database\Connection
     */
    protected static function getConnection()
    {
        $config = Kohana::$config->load('govi')->{static::$group};

        $resolver = new Connection_Resolver($config);

        static::$connection = $resolver->connection();
    }

}
