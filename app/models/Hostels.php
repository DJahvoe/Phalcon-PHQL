<?php

// namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class Hostels extends Model
{
    public $ID;

    public $summary_score;

    public $rating_band;

    public $atmosphere;

    public $cleanliness;

    public $facilities;

    public $location_y;

    public $security;

    public $staff;

    public $valueformoney;

    public $hostel_name;

    public $city;

    public $price_from;

    public $distance;

    public $lon;

    public $lat;

    public $like;

    public static function findHostelByID(string $params)
    {
        $sql = 'SELECT * FROM Hostels WHERE ID = ' . $params;
        $hostels = new Hostels();

        return new Resultset(
            null,
            $hostels,
            $hostels->getReadConnection()->query($sql)
        );
    }
}
