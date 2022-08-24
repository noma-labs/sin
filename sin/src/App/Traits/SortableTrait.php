<?php

namespace App\Traits;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

trait SortableTrait {
    public function scopeSortable($query, $column=null, $order=null) {
        if(Request::has('s') && Request::has('o'))
            return $query->orderBy(Request::input('s'), Request::input('o'));
        if($column != null && $order != null)
            return $query->orderBy($column, $order);
        else
            return $query;
    }

    public static function link_to_sorting_action($col, $title = null) {
        if (is_null($title)) {
            $title = str_replace('_', ' ', $col);
            $title = ucfirst($title);
        }

        $indicator = (Request::input('s') == $col ? (Request::input('o') === 'asc' ? '&uarr;' : '&darr;') : null);
        $parameters = array_merge(Request::input(), array('s' => $col, 'o' => (Request::input('o') === 'asc' ? 'desc' : 'asc')));

        return link_to_route(Route::currentRouteName(), "$title $indicator", $parameters);
    }
}
