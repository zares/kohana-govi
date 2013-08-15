<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Eloquent ORM for Kohana Framework.
 *
 * @package    Govi
 * @category   Kohana-module
 * @author     S.Zares <sergiozares@gmail.com>
 * @copyright  (c) 2013 S.Zares
 * @license    MIT License
 *
 */
use \Illuminate\Database\Capsule\Manager as Capsule;
use \Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Govi_Eloquent extends Eloquent {

    /**
     * Database config group.
     *
     * @var string
     */
    protected $group = 'default';

    /**
     * Create a new database connection.
     * Set the connection resolver instance.
     *
     * @return void
     */
    public function __construct()
    {
        $config = Kohana::$config->load('govi')->{$this->group};

        $capsule = new Capsule;

        $capsule->addConnection($config);

        $capsule->bootEloquent();
    }

}
