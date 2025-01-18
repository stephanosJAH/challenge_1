<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class HotelFilter extends ApiFilter
{  
    /**
     * The safe parameters.
     *
     * @var array
     */
    protected $safeParameters = [
        'name' => ['like', 'eq'],
        'description' => ['like'],
        'address' => ['like'],
        'rating' => ['eq', 'gt', 'lt', 'tle', 'gte'],
        'price_per_night' => ['eq', 'gt', 'lt', 'tle', 'gte']
    ];

    /**
     * The columns map.
     *
     * @var array
     */
    protected $columnsMap = [
        'name' => 'name',
        'description' => 'description',
        'address' => 'address',
        'rating' => 'rating',
        'price_per_night' => 'price_per_night'
    ];

    /**
     * The operators map.
     *
     * @var array
     */
    protected $operatorsMap = [
        'like' => 'like',
        'eq' => '=',
        'gt' => '>',
        'lt' => '<',
        'tle' => '<=',
        'gte' => '>=',
    ];

}
