<?php defined('SYSPATH') OR die('No direct script access.');

use \Illuminate\Database\ConnectionResolverInterface;

class Connection_Resolver implements ConnectionResolverInterface {

    /**
     * The PDO instance.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * The PDO driver name.
     *
     * @var string
     */
    protected $driver;

    /**
     * The table prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Database connection instances.
     *
     * @var Illuminate\Database\Connection
     */
    protected $connections = array();

    /**
     * Create a new connection resolver.
     *
     * @param  PDO     $pdo
     * @param  string  $prefix
     * @return void
     */
    public function __construct($config)
    {
        $this->driver = $config['driver'];
        $this->prefix = $config['prefix'];
        $this->pdo    = $this->db($config)->connect($config);
    }

    /**
     * Get a database connection instance.
     *
     * @param  string  $name
     * @return Illuminate\Database\Connection
     */
    public function connection($name = NULL)
    {
        return $this->getConnection($name);
    }

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection() {}

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name) {}

    /**
     * Get connection instance.
     *
     * @return Illuminate\Database\Connection
     */
    protected function getConnection($name = 'default')
    {
        if ( ! isset($this->connections[$name]))
        {
            $connection = new \Illuminate\Database\Connection($this->pdo, '', $this->prefix);

            switch ($this->driver)
            {
                case 'mysql':
                    $grammar = '\Illuminate\Database\Query\Grammars\MySqlGrammar';
                    break;
                case 'pgsql':
                    $grammar = '\Illuminate\Database\Query\Grammars\PostgresGrammar';
                    break;
                case 'sqlsrv':
                    $grammar = '\Illuminate\Database\Query\Grammars\SqlServerGrammar';
                    break;
                case 'sqlite':
                    $grammar = '\Illuminate\Database\Query\Grammars\SQLiteGrammar';
                    break;
                default:
                    throw new \InvalidArgumentException("Cannot determine grammar to use based on {$this->driver}.");
                    break;
            }

            $connection->setQueryGrammar(new $grammar);

            $this->connections[$name] = $connection;
        }

        return $this->connections[$name];
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connectors\ConnectorInterface
     */
    protected function db(array $config)
    {
        if ( ! isset($config['driver']))
        {
            throw new \InvalidArgumentException("A driver must be specified.");
        }

        switch ($config['driver'])
        {
            case 'mysql':
                return new \Illuminate\Database\Connectors\MySqlConnector;
            case 'pgsql':
                return new \Illuminate\Database\Connectors\PostgresConnector;
            case 'sqlite':
                return new \Illuminate\Database\Connectors\SQLiteConnector;
            case 'sqlsrv':
                return new \Illuminate\Database\Connectors\SqlServerConnector;
        }

        throw new \InvalidArgumentException("Unsupported driver [{$config['driver']}]");
    }

}
