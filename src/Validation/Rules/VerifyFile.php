<?php
/**
 * Created by PhpStorm.
 * User: stikks
 * Date: 8/5/16
 * Time: 11:51 AM
 */

namespace App\Validation\Rules;
use App\Models\Files;
use Respect\Validation\Rules\AbstractRule;


class VerifyFile extends AbstractRule
{

    public function validate($input)
    {
        // TODO: Implement validate() method.
        $file = Files::where('name', $input)->get();

        if ($file) {
            return true;
        }

        return false;
    }
}