<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 17:29
 */

namespace Podcaster\Resource\Feed;

use Podcaster\PodcasterParseTrait;

class Google implements \JsonSerializable
{
    use PodcasterParseTrait;

    protected $description;
    protected $author;
    protected $category;
    protected $explicit;
    protected $block;
}