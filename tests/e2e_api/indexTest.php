<?php
use PHPUnit\Framework\TestCase;
require ('vendor/autoload.php');

class indexTest extends TestCase
{
    private $http;

    public function setUp() : void
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://hoqutest.webmapp.it']);
    }

    public function tearDown() : void
    {
        $this->http = null;
    }

    public function testGet()
    {
        $response = $this->http->request('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);

        $r=json_decode($response->getBody(),TRUE);
        $this->assertTrue(isset($r['version']));
    }

}