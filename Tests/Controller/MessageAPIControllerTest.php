<?php

namespace b3da\EasyOpenSslBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageAPIControllerTest extends WebTestCase
{
//    public function testIndex()
//    {
//        $client = static::createClient();
//
//        $crawler = $client->request('GET', '/');
//
//        $this->assertContains('Hello World', $client->getResponse()->getContent());
//    }

    public function testClientCreate() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/eos/msg/api/client/create/');

        $this->assertContains('success', $client->getResponse()->getContent()['status']);
    }
}
