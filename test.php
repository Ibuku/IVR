<?php
    $envData = file_get_contents(__DIR__.'/env.json');
    $data = json_decode($envData);
    var_dump($data);
    exit();
