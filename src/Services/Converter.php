<?php

/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/13/16
 * Time: 2:35 PM
 */
namespace App\Services;

use FFMpeg;
use FFMpeg\Format\Audio\Wav;

class Converter
{
    protected $ffmpeg;

    static public function convert($file_path, $filename, $directory)
    {
        $ffmpeg = FFMpeg\FFMpeg::create();

        $audio = $ffmpeg->open($file_path);

        $format = new ADPCM();
        $format ->setAudioCodec('adpcm_ms');
//        $format = new Wav();

//        $format-> setAudioChannels(2)-> setAudioKiloBitrate(256);

//        $format->setAudioKiloBitrate(256);

        $audio->save($format, $directory. $filename. '.wav');
        
        return $audio;
    }
}