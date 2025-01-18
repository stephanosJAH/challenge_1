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
        'tour_id' => ['eq'],
        'hotel_id' => ['eq'],
        'customer_name' => ['like'],
        'customer_email' => ['like'],
        'number_of_people' => ['eq'],
        'booking_date' => ['eq', 'gt', 'lt', 'tle', 'gte'],
    ];

    /**
     * The columns map.
     *
     * @var array
     */
    protected $columnsMap = [
        'tour_id' => 'tour_id',
        'hotel_id' => 'hotel_id',
        'customer_name' => 'customer_name',
        'customer_email' => 'customer_email',
        'number_of_people' => 'number_of_people',
        'booking_date' => 'booking_date',
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
