<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 10/13/16
 * Time: 3:54 PM
 */

namespace App\Services;

use FFMpeg\Format\Audio\DefaultAudio;


class ADPCM extends DefaultAudio
{
    public function __construct()
    {
        $this->audioCodec = 'adpcm_ms';
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return array('adpcm_ms');
    }
}