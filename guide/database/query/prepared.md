### Prepared Statements

Using prepared statements allows you to write SQL queries manually while still escaping the query values automatically to prevent [SQL injection](http://wikipedia.org/wiki/SQL_Injection). Creating a query is simple:

    $query = DB::query(Database::SELECT, 'SELECT * FROM users WHERE username = :user');

The [DB::query] factory method creates a new [Database_Query] class for us, to allow method chaining. The query contains a `:user` parameter, which we can assign to a value:

    $query->param(':user', 'john');

[!!] Parameter names can be any string, as they are replaced using [strtr](http://php.net/strtr). It is highly recommended to **not** use dollars signs as parameter names to prevent confusion.

If you want to display the SQL that will be executed, simply cast the object to a string:

    echo Kohana::debug((string) $query);
    // Should display:
    // SELECT * FROM users WHERE username = 'john'

You can also update the `:user` parameter by calling [Database_Query::param] again:

    $query->param(':user', $_GET['search']);

[!!] If you want to set multiple parameters at once, you can use [Database_Query::parameters].

Once you have assigned something to each of the parameters, you can execute the query:

    $query->execute();

It is also possible to bind a parameter to a variable, using a [variable reference]((http://php.net/language.references.whatdo)). This can be extremely useful when running the same query many times:

    $query = DB::query(Database::INSERT, 'INSERT INTO users (username, password) VALUES (:user, :pass)')
        ->bind(':user', $username)
        ->bind(':pass', $password);

    foreach ($new_users as $username => $password)
    {
        $query->execute();
    }

In the above example, the variables `$username` and `$password` are changed for every loop of the `foreach` statement. When the parameter changes, it effectively changes the `:user` and `:pass` query parameters. Careful parameter binding can save a lot of code when it is used properly.
