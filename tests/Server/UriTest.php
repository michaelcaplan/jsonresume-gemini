<?php

namespace Server;

use michaelcaplan\JsonResume\Gemini\Server\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{

    public function test__getHost()
    {
        $test = 'gemini://rawtext.club/~sloum/spacewalk.gmi';
        $uri = new Uri($test);

        $this->assertEquals($uri->getHost(), 'rawtext.club');
    }

    public function test__toString()
    {
        $test = 'gemini://rawtext.club/~sloum/spacewalk.gmi';
        $uri = new Uri($test);

        $this->assertEquals($uri->__toString(), $test);
    }
}
