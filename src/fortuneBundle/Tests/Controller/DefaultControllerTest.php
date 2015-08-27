<?php

namespace fortuneBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/quotes');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        
        $data = json_decode($client->getResponse()->getContent(), true);
        
        $this->assertGreaterThan(0, $data);

        $this->assertArrayHasKey('author', $data[0]);
        $this->assertArrayHasKey('text', $data[0]);
        $this->assertArrayHasKey('date', $data[0]);
        
    }
    
    public function testEmail()
    {
       
        $client = static::createClient();

        $crawler = $client->request('POST', '/add/email', array('form'=>array('email' => 'bob@bob.com')));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('POST', '/add/email', array('form'=>array('email' => 'bob')));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
         
    }
        
    
}
