# Querying and fixing zero dates in MySQL

MySQL's zero date (`0000-00-00 00:00:00`) sorts before any real date, so you can find rows holding one by comparing against the lowest valid `DATETIME`, `1000-01-01 00:00:00`. This sidesteps the strict SQL modes that reject a literal `0000-00-00` in the query itself.

```php
$total = DB::table('orders')
    ->where('completed_at', '<', '1000-01-01 00:00:00')
    ->count();
```

Once the column is nullable, you can null them out with the same condition:

```php
DB::table('orders')
    ->where('completed_at', '<', '1000-01-01 00:00:00')
    ->update(['completed_at' => null]);
```

The modes doing the rejecting are `NO_ZERO_DATE` and `NO_ZERO_IN_DATE`, part of MySQL's `sql_mode`. In Laravel they're enabled by the `strict` flag on the connection in `config/database.php`:

```php
'mysql' => [
    // ...
    'strict' => true,
    'modes' => [
        'NO_ZERO_DATE',
        'NO_ZERO_IN_DATE',
        // ...
    ],
],
```
