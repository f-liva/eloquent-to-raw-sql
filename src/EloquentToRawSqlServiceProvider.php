<?php

namespace Fliva\EloquentToRawSql;

use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class EloquentToRawSqlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * Adds a macro method 'toRawSql' to the QueryBuilder class.
         *
         * This method converts an SQL query into a raw SQL string
         * by replacing placeholders with the corresponding bindings.
         *
         * Original idea by @PyaeSoneAungRgn (https://github.com/PyaeSoneAungRgn/to-raw-sql)
         *
         * @return string The final SQL query with the bound values replaced.
         */
        QueryBuilder::macro('toRawSql', function () {
            $bindings = $this->getBindings();

            foreach ($bindings as $key => $value) {
                if ($value instanceof DateTimeInterface) {
                    $bindings[$key] = "'{$value->format('Y-m-d H:i:s')}'";
                } elseif (is_string($value)) {
                    $bindings[$key] = "'{$value}'";
                } elseif (is_bool($value)) {
                    $bindings[$key] = $value ? 'true' : 'false';
                } elseif (is_null($value)) {
                    $bindings[$key] = 'NULL';
                }
            }

            return Str::replaceArray('?', $bindings, $this->toSql());
        });

        /**
         * Adds a macro to obtain the raw SQL query.
         *
         * @param  bool  $beautify  Indicates whether the SQL should be formatted for readability.
         *                       If true, uses SqlFormatter to enhance readability.
         * @return string The raw SQL query, formatted if $beautify is true.
         */
        EloquentBuilder::macro('toRawSql', function (bool $beautify = true) {
            $sql = $this->getQuery()->toRawSql();

            return $beautify ? (new SqlFormatter)->format($sql) : $sql;
        });
    }
}
