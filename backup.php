<?php
/**
 * Created by PhpStorm.
 * User: stikks-workstation
 * Date: 8/20/17
 * Time: 4:05 PM
 */
require __DIR__ . '/vendor/autoload.php';
use Aws\S3\S3Client;

// Instantiate the S3 client with your AWS credentials
$envData = file_get_contents(__DIR__.'/env.json');
$env = json_decode($envData);

$s3 = S3Client::factory(array(
    'credentials' => array(
        'key'    => $env->key,
        'secret' => $env->secret,
    ),
    'region'  => 'us-west-2',
    'version' => 'latest'
));

$bucket = 'tm30';

$zip = new ZipArchive;

$data = array(
    ["/opt/backup/es.zip", "/var/lib/docker/volumes/elasticsearch-data/*"],
    ["/opt/backup/redis.zip" , "/var/lib/docker/volumes/redis-data/*"],
    ["/opt/backup/postgres.zip" , "/var/lib/docker/volumes/postgres-data/*"],
    ["/opt/backup/agi-sounds.zip" , "/var/lib/docker/volumes/agi-sounds/*"],
    ["/opt/backup/agi-defaults.zip" , "/var/lib/docker/volumes/agi-defaults/*"],
    ["/opt/backup/files.zip" , "/var/lib/docker/volumes/files-data/*"],
    ["/opt/backup/freepbx.zip" , "/var/lib/docker/volumes/freepbx-data/*"]
);

foreach ($data as $v) {
    try {
        $exploded = explode('/', $v[0]);
        exec('zip -r '.$v[0].' '.$v[1]);
        $result = $s3->putObject(array(
            'Bucket'       => $bucket,
            'Key'          => end($exploded),
            'SourceFile'   => $v[0],
            'ACL'          => 'public-read',
            'StorageClass' => 'REDUCED_REDUNDANCY'
        ));

        echo $result['ObjectURL'];
    } catch (Exception $e) {
        echo $e;
    }
}