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
     * Database connection instances.
     *
     * @var \Illuminate\Database\Connection
     */
    protected static $connections = array();

    /**
    * Dynamically pass methods to the default connection.
    *
    * @param  string  $method
    * @param  array   $parameters
    * @return mixed
    */
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::connection(), $method), $parameters);
    }

    /**
    * Get a database connection instance.
    *
    * @param  string  $name  Connection name
    * @return \Illuminate\Database\Connection
    */
    public static function connection($name = 'default')
    {
        if ( ! isset(static::$connections[$name]))
        {
            $config = Kohana::$config->load('govi')->$name;

            $resolver = new Connection_Resolver($config);

            static::$connections[$name] = $resolver->connection();
        }

        return static::$connections[$name];
    }

    /**
     * Reconnect to the given database.
     *
     * @param  string  $name  Connection name
     * @return \Illuminate\Database\Connection
     */
    public static function reconnect($name = 'default')
    {
        unset(static::$connections[$name]);

        return static::connection($name);
    }

}
