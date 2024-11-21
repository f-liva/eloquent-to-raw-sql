<?php

namespace Fliva\EloquentToRawSql;

use DateTime;
use Doctrine\SqlFormatter\SqlFormatter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

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
         * Original idea by @therobfonz (https://twitter.com/kirschbaum_dev/status/1418590965368074241)
         * Refined by BinaryKitten (https://gist.github.com/BinaryKitten/2873e11daf3c0130b5a19f6b94315033)
         *
         * @return string The final SQL query with the bound values replaced.
         */
        QueryBuilder::macro('toRawSql', function () {
            return array_reduce(
                $this->getBindings(),
                static function ($sql, $binding) {
                    if ($binding instanceof DateTime) {
                        $binding = $binding->format('Y-m-d H:i:s');
                    }

                    return preg_replace(
                        '/\?/',
                        is_string($binding) ? "'" . $binding . "'" : $binding,
                        str_replace('"', '', $sql),
                        1
                    );
                },
                $this->toSql()
            );
        });

        /**
         * Adds a macro to obtain the raw SQL query.
         *
         * @param  bool  $beautify  Indicates whether the SQL should be formatted for readability.
         *                       If true, uses SqlFormatter to enhance readability.
         * @return string The raw SQL query, formatted if $beautify is true.
         */
        EloquentBuilder::macro('toRawSql', function (bool $beautify = false) {
            $sql = $this->getQuery()->toRawSql();

            return $beautify ? (new SqlFormatter)->format($sql) : $sql;
        });
    }
}
