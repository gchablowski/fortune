<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use fortuneBundle\Command\CreateClientCommand;

class CreateClientCommandTest extends KernelTestCase {

    public function testExecute() {
        $kernel = $this->createKernel();
        $kernel->boot();


        $application = new Application($kernel);
        $application->add(new CreateClientCommand);

        $command = $application->find('fortune:oauth-server:client:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/^Added a new client with public id.+/', $commandTester->getDisplay());
        
    }

}
