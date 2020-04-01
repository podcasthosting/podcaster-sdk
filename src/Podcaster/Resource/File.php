<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 15:54
 */

namespace Podcaster\Resource;

use Podcaster\PodcasterParseTrait;

class File implements \JsonSerializable
{
    protected $title;

    use PodcasterParseTrait;

    public function __construct()
    {
    }
}
