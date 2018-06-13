<?php
/**
 * User: fabio
 * Date: 13.06.18
 * Time: 15:54
 */

namespace Podcaster\Resource;

use Podcaster\PodcasterParseTrait;

class Feed implements \JsonSerializable
{
    protected $title;
    protected $link;
    protected $description;
    protected $author;
    protected $email;
    protected $copyright;
    protected $language;
    protected $category;
    protected $itunes;

    use PodcasterParseTrait;

    public function __construct()
    {
        $this->itunes = new Itunes();
    }
}