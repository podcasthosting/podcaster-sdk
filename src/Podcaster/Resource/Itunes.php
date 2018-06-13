<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 17:29
 */

namespace Podcaster\Resource;

use Podcaster\PodcasterParseTrait;

class Itunes implements \JsonSerializable
{
    use PodcasterParseTrait;

    protected $subtitle;
    protected $explicit;
    protected $type;
    protected $complete;
    protected $category = [];
    protected $block;
    protected $newFeedUrl;
}