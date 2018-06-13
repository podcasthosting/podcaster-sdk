<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 12:44
 */

namespace Podcaster\Exceptions;


class InvalidStateException extends \Exception
{
    /**
     * InvalidStateException constructor.
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}