<?php
// Before Laravel 10.15.0
function prepareQuery(QueryExecuted $query): string
{
    return count($query->bindings) > 0
        ? vsprintf(str_replace(['%', '?'], ['%%', '%s'], $query->sql), array_map(fn ($value) => (is_numeric($value) ? $value : "'" . $value . "'"), $query->bindings))
        : $query->sql;
}

prepareQuery($query);

// After Laravel 10.15.0
function prepareSql(QueryExecuted $query): string
{
    return $query->connection->getQueryGrammar()->substituteBindingsIntoRawSql(
        $query->sql,
        $query->connection->prepareBindings($query->bindings),
    );  
}

// Or from a built query:
$query->toRawSql();