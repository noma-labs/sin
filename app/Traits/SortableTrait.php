<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;

trait SortableTrait
{
    public static function link_to_sorting_action($col, $title = null): HtmlString
    {
        if (is_null($title)) {
            $title = str_replace('_', ' ', $col);
            $title = ucfirst($title);
        }

        $indicator = (Request::input('s') === $col ? (Request::input('o') === 'asc' ? '&uarr;' : '&darr;') : null);
        $parameters = array_merge(Request::input(), ['s' => $col, 'o' => (Request::input('o') === 'asc' ? 'desc' : 'asc')]);

        $url = route(Route::currentRouteName(), $parameters);

        return new HtmlString("<a href=\"$url\">$title $indicator</a>");
    }

    protected function scopeSortable($query, $column = null, $order = null): Builder
    {
        if (Request::has('s') && Request::has('o')) {
            return $query->orderBy(Request::input('s'), Request::input('o'));
        }
        if ($column !== null && $order !== null) {
            return $query->orderBy($column, $order);
        }

        return $query;

    }
}
