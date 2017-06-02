<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 8:07 AM
 */
namespace App\Middleware;

class Middleware
{

    protected $container;

    public function __construct($container)
    {

        $this->container = $container;
    }

}