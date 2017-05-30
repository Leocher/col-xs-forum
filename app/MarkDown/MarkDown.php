<?php

namespace App\MarkDown;

class MarkDown
{
    protected $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function markdowm($text)
    {
        $html = $this->parser->makeHtml($text);
        return $html;
    }
}