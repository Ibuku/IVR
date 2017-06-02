<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/30/16
 * Time: 6:52 PM
 */
shell_exec('plink -v -x -a -T -C -noagent -ssh -L 127.0.0.1:8970:10.64.33.230:80 root@campaign.atp-sevas.com');