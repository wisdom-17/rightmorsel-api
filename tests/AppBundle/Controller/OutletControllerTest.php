<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OutletControllerTest extends WebTestCase
{
    public function testNearest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'api/v1/outlets/nearest/-0.01045010/51.56263300');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
