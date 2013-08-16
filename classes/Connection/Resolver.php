<?php defined('SYSPATH') OR die('No direct script access.');

use \Illuminate\Database\ConnectionResolverInterface;

class Connection_Resolver implements ConnectionResolverInterface {

    /**
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = array();

    /**
     * Constructs a new connection resolver.
     *
     * @param  array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a database connection instance.
     *
     * @param  string  $name
     * @return Illuminate\Database\Connection
     */
    public function connection($name = NULL)
    {
        return $this->getConnection();
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
    protected function getConnection()
    {
        $driver = $this->config['driver'];

        $pdo = $this->db($driver)->connect($this->config);

        $connection = new \Illuminate\Database\Connection($pdo, '', $this->config['prefix']);

        switch ($driver)
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
                throw new \InvalidArgumentException("Cannot determine grammar to use based on {$driver}.");
                break;
        }

        $connection->setQueryGrammar(new $grammar);

        return $connection;
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Database\Connectors\ConnectorInterface
     */
    protected function db($driver)
    {
        if ( ! isset($driver))
        {
            throw new \InvalidArgumentException("A driver must be specified.");
        }

        switch ($driver)
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

        throw new \InvalidArgumentException("Unsupported driver [{$driver}]");
    }

}
