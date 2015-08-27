<?php

namespace fortuneBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    protected function CreateApp() {
        // create kernel to have service
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel;
    }

    public function testIndex() {
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

    public function testEmail() {

        $client = static::createClient();
         
        $crawler = $client->request('POST', '/add/email', array('fortunebundle_email' => array('email' => 'bob')));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $client = static::createClient();

        $crawler = $client->request('POST', '/add/email', array('fortunebundle_email' => array('email' => 'bob@gmail.com')));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //verify that the insert be done
        $kernel = $this->CreateApp();
        
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $getInsert = $this->em->getRepository('fortuneBundle:Email')->findBy(array('email' => 'bob@gmail.com'));

        $this->assertCount(1, $getInsert);
        
        //suppress mock on database
        $this->em->remove($getInsert[0]);
        $this->em->flush();
    }

}
