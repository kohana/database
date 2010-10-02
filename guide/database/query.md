## Making Queries {#making_queries}

There are two different ways to make queries. The simplest way to make a query is to use the [Database_Query], via [DB::query], to create queries. These queries are called "prepared statements" and allow you to set query parameters which are automatically escaped. The second way to make a query is by building the query using method calls. This is done using the [query builder](#query_building).

[!!] All queries are run using the `execute` method, which accepts a [Database] object or instance name. See [Database_Query::execute] for more information.

#### Database Functions {#database_functions}

Eventually you will probably run into a situation where you need to call `COUNT` or some other database function within your query. The query builder supports these functions in two ways. The first is by using quotes within aliases:

    $query = DB::select(array('COUNT("username")', 'total_users'))->from('users');

This looks almost exactly the same as a standard `AS` alias, but note how the column name is wrapped in double quotes. Any time a double-quoted value appears inside of a column name, **only** the part inside the double quotes will be escaped. This query would generate the following SQL:

    SELECT COUNT(`username`) AS `total_users` FROM `users`

#### Complex Expressions

Quoted aliases will solve most problems, but from time to time you may run into a situation where you need a complex expression. In these cases, you will need to use a database expression created with [DB::expr].  A database expression is taken as direct input and no escaping is performed.