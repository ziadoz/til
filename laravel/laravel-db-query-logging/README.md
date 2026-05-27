# Logging Database and Eloquent Queries

Laravel's `DB` facade provides methods to enable, retrieve, and flush a query log for any database connection.

```php
use Illuminate\Support\Facades\DB;

// Enable query logging on the default or a specific connection.
DB::enableQueryLog();
DB::connection('mysql-replica')->enableQueryLog();

// Get the query log on the default or a specific connection.
DB::getQueryLog();
DB::connection('mysql-replica')->getQueryLog();

// Get the raw query log (with bindings interpolated) on the default or a specific connection.
DB::getRawQueryLog();
DB::connection('mysql-replica')->getRawQueryLog();

// Flush the query log on the default or a specific connection.
DB::flushQueryLog();
DB::connection('mysql-replica')->flushQueryLog();

// Disable query logging on the default or a specific connection.
DB::disableQueryLog();
DB::connection('mysql-replica')->disableQueryLog();
```

`getQueryLog()` returns an array of arrays, each with `query`, `bindings`, and `time` keys. `getRawQueryLog()` returns the same structure but with bindings already substituted into the SQL string, which is more useful for debugging.

You can also listen to every query as it executes using `DB::listen()`:

```php
DB::listen(function ($query) {
    logger($query->toRawSql());
});
```

This is useful inside a service provider or middleware when you want to log all queries for a request without manually wrapping code with `enableQueryLog()` / `getQueryLog()`.
