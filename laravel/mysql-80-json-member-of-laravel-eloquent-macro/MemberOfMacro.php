<?php
use Illuminate\Database\Query\Builder;

$memberOf = function ($value, string $column, string $path = '$', $boolean = 'and') {
    return $this->whereRaw(
        sprintf(
            '? MEMBER OF(JSON_EXTRACT(%s, "%s"))',
            $this->getGrammar()->wrap($column),
            $path
        ),
        [$value],
        $boolean
    );
};

Builder::macro('whereMemberOf', $memberOf);
Builder::macro('whereNotMemberOf', $memberOf);

Model::query()->whereMemberOf('a_json_column', 'value', '$[*]');
Model::query()->whereNotMemberOf('a_json_column', 'value', '$.field');