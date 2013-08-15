<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Illuminate\Database session driver.
 *
 * @package    Govi
 * @category   Kohana-module
 * @author     S.Zares <sergiozares@gmail.com>
 * @copyright  (c) 2013 S.Zares
 * @license    MIT License
 *
 * --------------------------------------
 |  CREATE TABLE `sessions` (
 |    `session_id` varchar(24) NOT NULL,
 |    `user_id` int(10) unsigned NOT NULL DEFAULT '0',
 |    `ip_address` varchar(128) DEFAULT '',
 |    `user_agent` varchar(128) DEFAULT '',
 |    `last_active` int(10) unsigned NOT NULL,
 |    `contents` text NOT NULL,
 |    PRIMARY KEY (`session_id`),
 |    KEY `last_active` (`last_active`),
 |    KEY `user_id` (`user_id`)
 |  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * --------------------------------------*/

abstract class Govi_Session_Database extends Session {

    protected $session_id;
    protected $update_id;
    protected $table;

    protected $columns = array(
        'session_id'  => ':session_id',
        'user_id'     => ':user_id',
        'ip_address'  => ':ip_address',
        'user_agent'  => ':user_agent',
        'last_active' => ':last_active',
        'contents'    => ':contents',
    );


    public function __construct(array $config = null, $id = null)
    {
        $this->table = $config['table'] ?: 'sessions';

        parent::__construct($config, $id);

        $gc = isset($config['gc']) ? $config['gc'] : 500 ;

        if (mt_rand(0, $gc) == $gc)
        {
            $this->garbageCollection();
        }
    }

    /**
     * Returns the current session id.
     *
     * @return  string
     */
    public function id()
    {
        return $this->session_id;
    }

    /**
     * Loads the raw session data string and returns it.
     *
     * @param   string  $id session id
     * @return  string
     */
    protected function _read($id = null)
    {
        if ($id OR $id = Cookie::get($this->_name))
        {
            $query = "SELECT contents FROM ".$this->table." WHERE session_id = ?";

            $result = DB::selectOne($query, array($id));

            if (count($result) === 1)
            {
                $this->session_id = $this->update_id = $id;

                return $result['contents'];
            }
        }

        $this->_regenerate();
    }

    /**
     * Generate a new session id and return it.
     *
     * @return  void
     */
    protected function _regenerate()
    {
        $query = "SELECT session_id FROM ".$this->table." WHERE session_id = ?";

        do
        {
            $id = str_replace('.', '-', uniqid(null, true));

            $result = DB::selectOne($query, array($id));
        }
        while (count($result) === 1);

        $this->session_id = $id;
    }

    /**
     * Writes the current session.
     *
     * @return  void
     */
    protected function _write()
    {
        $data = $this->_data;

        $keys = array_keys($this->columns);
        $vals = array_values($this->columns);

        $bindings = array(
            $vals[0] => $this->session_id,
            $vals[1] => isset($data['current_user']) ? $data['current_user'] : 0,
            $vals[2] => Request::$client_ip,
            $vals[3] => Request::$user_agent,
            $vals[4] => $data['last_active'],
            $vals[5] => $this->__toString()
        );

        if ($this->update_id === null)
        {
            $columns = '('.implode(', ', $keys).')';
            $values  = '('.implode(', ', $vals).')';

            $query = "INSERT INTO ".$this->table." ".$columns." VALUES ".$values;

            $result = DB::insert($query, $bindings);
        }
        else
        {
            if ($this->update_id == $this->session_id)
            {
                unset($this->columns['session_id']);
                unset($bindings[$vals[0]]);
            }

            $updates = '';

            foreach ($this->columns AS $key => $val)
            {
                $updates .= $key.' = '.$val.', ';
            }

            $updates = substr($updates, 0, -2);

            $query = "UPDATE ".$this->table." SET ".$updates." WHERE session_id = :old_session_id";

            $bindings += array(':old_session_id' => $this->update_id);

            $result = DB::update($query, $bindings);
        }

        if ( (int) $result === 1)
        {
            $this->update_id = $this->session_id;

            Cookie::set($this->_name, $this->session_id, $this->_lifetime);
        }
    }

    /**
     * Destroys the current session.
     *
     * @return  boolean
     */
    protected function _destroy()
    {
        if ($this->update_id === null) return;

        $query = "DELETE FROM ".$this->table." WHERE session_id = ?";

        try
        {
            DB::delete($query, array($this->update_id));

            Cookie::delete($this->_name);
        }
        catch (Exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Makes garbage collection.
     *
     * @return  void
     */
    protected function garbageCollection()
    {
        if ($this->_lifetime)
        {
            $expires = $this->_lifetime;
        }
        else
        {
            $expires = Date::MONTH;
        }

        $query = "DELETE FROM ".$this->table." WHERE last_active < ?";

        DB::delete($query, array(time() - $expires));
    }

    /**
     * Restart the session.
     *
     * @return  boolean
     */
    protected function _restart()
    {
        $this->_regenerate();

        return true;
    }

}
