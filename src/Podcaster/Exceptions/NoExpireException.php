<?php
/**
 * Created by PhpStorm.
 * User: fabio
 * Date: 13.06.18
 * Time: 14:46
 */

namespace Podcaster\Token;


class NoExpireException extends \Exception
{
    /**
     * NoExpireException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}