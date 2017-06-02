<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 9/28/16
 * Time: 2:00 AM
 */
namespace App\Controllers;

use \Slim\Views\Twig as View;

class BaseController
{
    protected $container;

    public function __construct($container)
    {

        $this->container = $container;
    }

    public function __get($name)
    {
        if ($this->container->{$name}) {
            return $this->container->{$name};
        }
    }
}