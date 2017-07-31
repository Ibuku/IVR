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

    public function send_via_remote($address, $username, $password, $localName, $remoteName) {
        $connection = ssh2_connect($address, 22);
        ssh2_auth_password($connection, $username, $password);

        ssh2_scp_send($connection, $localName, $remoteName, 0777);
    }
}