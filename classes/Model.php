<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Model {

    /**
     * The prefix to use for all model's class names
     *
     * @var string
     */
    protected static $prefix = 'Model_';

    /**
     * Creates and returns a new model
     * Model name must be passed with its' original casing, e.g.
     *
     *    $post = Model::factory('Blog_Post');
     *
     * @param   string  $model  Model name
     * @return  \Illuminate\Database\Eloquent\Model
     */
    public static function factory($model)
    {
        $class = static::$prefix.$model;

        return new $class;
    }

    /**
     * ---------------------------------------
     * And... Sample usage
     * ---------------------------------------
     *
     *    $post = Model::factory('Post');
     *
     *    try
     *    {
     *        $result = $post->findOrFail(2013);
     *    }
     *    catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e)
     *    {
     *        throw new HTTP_Exception_404;
     *    }
     *
     * --------------------------------------*/

}
