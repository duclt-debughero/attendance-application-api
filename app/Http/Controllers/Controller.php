<?php

namespace App\Http\Controllers;

use App\Libs\ValueUtil;
use Illuminate\Support\Arr;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller
{
    /**
     * Handle pagination
     *
     * @param object $query
     */
    public function pagination($query) {
        $limit = ValueUtil::get('common.pagination_limit');
        $urlQuery = Arr::map(request()->query(), function ($value) {
            return $value ?? '';
        });

        if (! is_array($query)) {
            return $query->paginate($limit)->appends($urlQuery);
        }

        $currentPage = request()->input('page', 1);
        $offset = ($currentPage - 1) * $limit;
        $itemsForCurrentPage = array_slice($query, $offset, $limit);

        return new LengthAwarePaginator(
            $itemsForCurrentPage,
            count($query),
            $limit,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => $urlQuery,
            ]
        );
    }
}
