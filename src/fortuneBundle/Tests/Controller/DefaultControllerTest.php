<?php

namespace fortuneBundle\Tests\Controller;

use fortuneBundle\Command\CreateClientCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    protected function CreateApp() {
        // create kernel to have service
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel;
    }

    public function testConnect() {

        $client = static::createClient();

        $crawler = $client->request('GET', '/oauth/v2/token');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        //recuperate an clientid and an secret
        $kernel = $this->CreateApp();

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $getClients = $this->em->getRepository('fortuneBundle:Client')->findAll();

        $crawler = $client->request('GET', '/oauth/v2/token?client_id=' . $getClients[0]->getId() . '_' . $getClients[0]->getRandomId() . '&client_secret=' . $getClients[0]->getSecret() . '&grant_type=client_credentials');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('access_token', $data);
        $this->assertArrayHasKey('expires_in', $data);

        return $data['access_token'];
    }

    /**
     * @depends testConnect
     */
    public function testIndex($access_token) {
        $client = static::createClient();

        $crawler = $client->request('GET', '/quotes?access_token=' . $access_token);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertGreaterThan(0, $data);

        $this->assertArrayHasKey('author', $data[0]);
        $this->assertArrayHasKey('text', $data[0]);
        $this->assertArrayHasKey('date', $data[0]);
        
        return $access_token;
    }

    /**
     * @depends testIndex
     */
    public function testEmail($access_token) {

        $client = static::createClient();

        $crawler = $client->request('POST', '/add/email?access_token=' . $access_token, array('fortunebundle_email' => array('email' => 'bob')));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $client = static::createClient();

        $crawler = $client->request('POST', '/add/email?access_token=' . $access_token, array('fortunebundle_email' => array('email' => 'bob@gmail.com')));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //verify that the insert be done
        $kernel = $this->CreateApp();

        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $getInsert = $this->em->getRepository('fortuneBundle:Email')->findBy(array('email' => 'bob@gmail.com'));

        $this->assertCount(1, $getInsert);

        //suppress mock on database
        $this->em->remove($getInsert[0]);
        $this->em->flush();
        
        return $access_token;
    }
    /**
     * @depends testEmail
     */
      public function testActivation($access_token) {

      $client = static::createClient();

      $crawler = $client->request('POST', '/add/email?access_token=' . $access_token, array('fortunebundle_email' => array('email' => 'bob@gmail.com')));

      //verify that the insert be done
      $kernel = $this->CreateApp();

      $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

      $getInsert = $this->em->getRepository('fortuneBundle:Email')->findOneBy(array('email' => 'bob@gmail.com'));

      //test activation
      $crawler = $client->request('PUT', '/email/activate/' . $getInsert->getToken().'?access_token=' . $access_token);

      $this->assertEquals(200, $client->getResponse()->getStatusCode());

      $crawler = $client->request('PUT', '/email//activate' . $getInsert->getToken().'?access_token=' . $access_token);

      $this->assertEquals(404, $client->getResponse()->getStatusCode());

      //test activation
      $crawler = $client->request('PUT', '/email/desactivate/' . $getInsert->getToken().'?access_token=' . $access_token);

      $this->assertEquals(200, $client->getResponse()->getStatusCode());

      $crawler = $client->request('PUT', '/email/desactivate/' . $getInsert->getToken().'?access_token=' . $access_token);

      $this->assertEquals(404, $client->getResponse()->getStatusCode());

      //test message

      $crawler = $client->request('PUT', '/email/activate/' . $getInsert->getToken().'?access_token=' . $access_token);
      $data = json_decode($client->getResponse()->getContent(), true);

      $this->assertGreaterThan(0, $data);

      $this->assertArrayHasKey('code', $data);
      $this->assertArrayHasKey('message', $data);
      $this->assertArrayHasKey('errors', $data);

      $this->assertEquals("bob@gmail.com have been activated", $data['message']);

      //suppress mock on database
      $this->em->remove($getInsert);
      $this->em->flush();
      } 
}
