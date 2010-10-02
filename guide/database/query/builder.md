### Query Building {#query_building}

Creating queries dynamically using objects and methods allows queries to be written very quickly in an agnostic way. Query building also adds identifier (table and column name) quoting, as well as value quoting.

[!!] At this time, it is not possible to combine query building with prepared statements.

#### SELECT

Each type of database query is represented by a different class, each with their own methods. For instance, to create a SELECT query, we use [DB::select]:

    $query = DB::select()->from('users')->where('username', '=', 'john');

By default, [DB::select] will select all columns (`SELECT * ...`), but you can also specify which columns you want returned:

    $query = DB::select('username', 'password')->from('users')->where('username', '=', 'john');

Now take a minute to look at what this method chain is doing. First, we create a new selection object using the [DB::select] method. Next, we set table(s) using the `from` method. Last, we search for a specific records using the `where` method. We can display the SQL that will be executed by casting the query to a string:

    echo Kohana::debug((string) $query);
    // Should display:
    // SELECT `username`, `password` FROM `users` WHERE `username` = 'john'

Notice how the column and table names are automatically escaped, as well as the values? This is one of the key benefits of using the query builder.

It is also possible to create `AS` aliases when selecting:

    $query = DB::select(array('username', 'u'), array('password', 'p'))->from('users');

This query would generate the following SQL:

    SELECT `username` AS `u`, `password` AS `p` FROM `users`

#### INSERT

To create records into the database, use [DB::insert] to create an INSERT query:

    $query = DB::insert('users', array('username', 'password'))->values(array('fred', 'p@5sW0Rd'));

This query would generate the following SQL:

    INSERT INTO `users` (`username`, `password`) VALUES ('fred', 'p@5sW0Rd')

#### UPDATE

To modify an existing record, use [DB::update] to create an UPDATE query:

    $query = DB::update('users')->set(array('username' => 'jane'))->where('username', '=', 'john');

This query would generate the following SQL:

    UPDATE `users` SET `username` = 'jane' WHERE `username` = 'john'

#### DELETE

To remove an existing record, use [DB::delete] to create a DELETE query:

    $query = DB::delete('users')->where('username', 'IN', array('john', 'jane'));

This query would generate the following SQL:

    DELETE FROM `users` WHERE `username` IN ('john', 'jane')