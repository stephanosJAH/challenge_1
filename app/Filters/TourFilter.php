<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class BookingFilter extends ApiFilter
{  
    /**
     * The safe parameters.
     *
     * @var array
     */
    protected $safeParameters = [
        'name' => ['like', 'eq'],
        'description' => ['like'],
        'price' => ['eq', 'gt', 'lt', 'tle', 'gte'],
        'start_date' => ['eq', 'gt', 'lt', 'tle', 'gte'],
        'end_date' => ['eq', 'gt', 'lt', 'tle', 'gte'],
    ];

    /**
     * The columns map.
     *
     * @var array
     */
    protected $columnsMap = [
        'name' => 'name',
        'description' => 'description',
        'price' => 'price',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
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
