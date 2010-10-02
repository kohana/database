## Configuration

After the module has been enabled you will need to provide a configuration file so that the module knows how to connect to your database. An example config file can be found at `modules/database/config/database.php`.

The structure of a database configuration group, called an "instance", looks like this:

    string INSTANCE_NAME => array(
        'type'         => string DATABASE_TYPE,
        'connection'   => array CONNECTION_ARRAY,
        'table_prefix' => string TABLE_PREFIX,
        'charset'      => string CHARACTER_SET,
        'profiling'    => boolean QUERY_PROFILING,
    ),

[!!] Multiple instances of these settings can be defined within the configuration file.

Understanding each of these settings is important.

INSTANCE_NAME
:  Connections can be named anything you want, but you should always have at least one connection called "default".

DATABASE_TYPE
:  One of the installed database drivers. Kohana comes with "mysql" and "pdo" drivers.

CONNECTION_ARRAY
:  Specific driver options for connecting to your database. (Driver options are explained [below](#connection_settings).)

TABLE_PREFIX
:  Prefix that will be added to all table names by the [query builder](#query_building).

QUERY_PROFILING
:  Enables [profiling](debugging.profiling) of database queries.

### Example

The example file below shows 2 MySQL connections, one local and one remote.

    return array
    (
        'default' => array
        (
            'type'       => 'mysql',
            'connection' => array(
                'hostname'   => 'localhost',
                'username'   => 'dbuser',
                'password'   => 'mypassword',
                'persistent' => FALSE,
                'database'   => 'my_db_name',
            ),
            'table_prefix' => '',
            'charset'      => 'utf8',
            'profiling'    => TRUE,
        ),
        'remote' => array(
            'type'       => 'mysql',
            'connection' => array(
                'hostname'   => '55.55.55.55',
                'username'   => 'remote_user',
                'password'   => 'mypassword',
                'persistent' => FALSE,
                'database'   => 'my_remote_db_name',
            ),
            'table_prefix' => '',
            'charset'      => 'utf8',
            'profiling'    => TRUE,
        ),
    );

### Connection Settings {#connection_settings}

Every database driver has different connection settings.

#### MySQL

A MySQL database can accept the following options in the `connection` array:

Type      | Option     |  Description               | Default value
----------|------------|----------------------------| -------------------------
`string`  | hostname   | Hostname of the database   | `localhost`
`integer` | port       | Port number                | `NULL`
`string`  | socket     | UNIX socket                | `NULL`
`string`  | username   | Database username          | `NULL`
`string`  | password   | Database password          | `NULL`
`boolean` | persistent | Persistent connections     | `FALSE`
`string`  | database   | Database name              | `kohana`

#### PDO

A PDO database can accept these options in the `connection` array:

Type      | Option     |  Description               | Default value
----------|------------|----------------------------| -------------------------
`string`  | dsn        | PDO data source identifier | `localhost`
`string`  | username   | Database username          | `NULL`
`string`  | password   | Database password          | `NULL`
`boolean` | persistent | Persistent connections     | `FALSE`

[!!] If you are using PDO and are not sure what to use for the `dsn` option, review [PDO::__construct](http://php.net/pdo.construct).

## Connections and Instances {#connections}

Each configuration group is referred to as a database instance. Each instance can be accessed by calling [Database::instance]:

    $default = Database::instance();
    $remote  = Database::instance('remote');

To disconnect the database, simply destroy the object:

    unset($default, Database::$instances['default']);

If you want to disconnect all of the database instances at once:

    Database::$instances = array();