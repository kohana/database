# Query Builder

Creating queries dynamically using objects and methods allows queries to be written very quickly in an agnostic way. Query building also adds identifier (table and column name) quoting, as well as value quoting.

[!!] At this time, it is not possible to combine query building with prepared statements.

## Select

Each type of database query is represented by a different class, each with their own methods. For instance, to create a SELECT query, we use [DB::select] which is a shortcut to return a new chainable [Database_Query_Builder_Select] object:

    $query = DB::select()->from('users')->where('username', '=', 'john');

By default, [DB::select] will select all columns (`SELECT * ...`), but you can also specify which columns you want returned by passing parameters to [DB::select]:

    $query = DB::select('username', 'password')->from('users')->where('username', '=', 'john');

Now take a minute to look at what this method chain is doing. First, we create a new selection object using the [DB::select] method. Next, we set table(s) using the `from` method. Last, we search for a specific records using the `where` method. We can display the SQL that will be executed by casting the query to a string:

    echo Kohana::debug((string) $query);
    // Should display:
    // SELECT `username`, `password` FROM `users` WHERE `username` = 'john'

Notice how the column and table names are automatically escaped, as well as the values? This is one of the key benefits of using the query builder.

### Select as

It is also possible to create `AS` aliases when selecting, by passing an array as each parameter to [DB::select]:

    $query = DB::select(array('username', 'u'), array('password', 'p'))->from('users');

This query would generate the following SQL:

    SELECT `username` AS `u`, `password` AS `p` FROM `users`

[!!] For a complete list of methods available while building a select query see [Database_Query_Builder_Select].

## Insert

To create records into the database, use [DB::insert] to create an INSERT query:

    $query = DB::insert('users', array('username', 'password'))->values(array('fred', 'p@5sW0Rd'));

This query would generate the following SQL:

    INSERT INTO `users` (`username`, `password`) VALUES ('fred', 'p@5sW0Rd')

[!!] For a complete list of methods available while building an insert query see [Database_Query_Builder_Insert].

## Update

To modify an existing record, use [DB::update] to create an UPDATE query:

    $query = DB::update('users')->set(array('username' => 'jane'))->where('username', '=', 'john');

This query would generate the following SQL:

    UPDATE `users` SET `username` = 'jane' WHERE `username` = 'john'
	
[!!] For a complete list of methods available while building an update query see [Database_Query_Builder_Update].

## Delete

To remove an existing record, use [DB::delete] to create a DELETE query:

    $query = DB::delete('users')->where('username', 'IN', array('john', 'jane'));

This query would generate the following SQL:

    DELETE FROM `users` WHERE `username` IN ('john', 'jane')

[!!] For a complete list of methods available while building a dalete query see [Database_Query_Builder_Delete].

## Advanced Queries

### Joins

	// todo

### Database Functions

Eventually you will probably run into a situation where you need to call `COUNT` or some other database function within your query. The query builder supports these functions in two ways. The first is by using quotes within aliases:

    $query = DB::select(array('COUNT("username")', 'total_users'))->from('users');

This looks almost exactly the same as a standard `AS` alias, but note how the column name is wrapped in double quotes. Any time a double-quoted value appears inside of a column name, **only** the part inside the double quotes will be escaped. This query would generate the following SQL:

    SELECT COUNT(`username`) AS `total_users` FROM `users`

### Complex Expressions

Quoted aliases will solve most problems, but from time to time you may run into a situation where you need a complex expression or other database functions. In these cases, you will need to use a database expression created with [DB::expr].  A database expression is taken as direct input and no escaping is performed.

	// example goes here, something like SET `count` = count + 1

[!!] You must validate or escape any user input as DB::expr will obviously not escape it for you.