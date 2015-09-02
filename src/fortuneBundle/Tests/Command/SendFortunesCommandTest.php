<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use fortuneBundle\Command\SendFortunesCommand;

class SendFortunesCommandTest extends KernelTestCase {


    public function testExecute() {
         // create kernel to have service
        $kernel = $this->createKernel();
        $kernel->boot();

        
        $application = new Application($kernel);
        $application->add(new SendFortunesCommand());
        

        $command = $application->find('fortune:SendFortunes');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/.../', $commandTester->getDisplay());

       
    }

}
