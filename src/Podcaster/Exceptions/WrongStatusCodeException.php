<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 15:37
 */

namespace Podcaster;


class WrongStatusCodeException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}