<?php

use Illuminate\Support\Facades\DB;

// Enable query logging on the default or a specific connection.
DB::enableQueryLog();
DB::connection('mysql-replica')->enableQueryLog();

// Get the query log on the default or a specific connection.
DB::getQueryLog();
DB::connection('mysql-replica')->getQueryLog();

// Get the raw query log on the default or a specific connection.
DB::getRawQueryLog();
DB::connection('mysql-replica')->getRawQueryLog();

// Flush the query log on the default or a specific connection.
DB::flushQueryLog();
DB::connection('mysql-replica')->flushQueryLog();

// Disable the query log on the default or a specific connection.
DB::disableQueryLog();
DB::connection('mysql-replica')->disableQueryLog();
