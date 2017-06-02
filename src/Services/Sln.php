<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/13/16
 * Time: 3:52 PM
 */

namespace App\Services;

use FFMpeg\Format\Audio\DefaultAudio;

/**
 * The Flac audio format
 */
class Sln extends DefaultAudio
{
    public function __construct()
    {
        $this->audioCodec = 'slin32';
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return array('slin32');
    }
}