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

    static public function run_remote_command($session, $command) {
        $srv = ssh2_exec($session, $command);
        if (!$srv) {
            return false;
        }
        return true;
    }

    static public function create_remotely($address, $username, $password, $directory) {

        try{
            $connection = ssh2_connect($address, 22);
            ssh2_auth_password($connection, $username, $password);
            $sftp = ssh2_sftp($connection);
            ssh2_sftp_mkdir($sftp, $directory);

            $change = static::run_remote_command($connection, "chown -R asterisk. ". $directory);
            if (!$change) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    static public function send_via_remote($address, $username, $password, $localName, $remoteName) {
        try {
            $connection = ssh2_connect($address, 22);

            $auth = ssh2_auth_password($connection, $username, $password);
            if (!$auth) {
                return false;
            }

            $transfer = ssh2_scp_send($connection, $localName, $remoteName, 0777);
            if (!$transfer) {
                return false;
            }

            $change = static::run_remote_command($connection, "chown -R asterisk. ". $remoteName);
            if (!$change) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            error_log($e);
            return false;
        }
    }

    static public function rename_remotely($address, $username, $password, $currentDir, $newDir) {
        try {
            $connection = ssh2_connect($address, 22);

            $auth = ssh2_auth_password($connection, $username, $password);
            if (!$auth) {
                return false;
            }

            $sftp = ssh2_sftp($connection);

            $transfer = ssh2_sftp_rename($sftp, $currentDir, $newDir);
            if (!$transfer) {
                return false;
            }

            $change = static::run_remote_command($connection, "chown -R asterisk. ". $newDir);
            if (!$change) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}