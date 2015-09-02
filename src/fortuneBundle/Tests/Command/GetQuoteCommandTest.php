<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use fortuneBundle\Command\GetQuoteCommand;
use fortuneBundle\Command\SetQuoteCommand;

class GetQuoteCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        
        $application = new Application($kernel);
        $application->add(new GetQuoteCommand());
        $application->add(new SetQuoteCommand());

        $command = $application->find('fortune:getQuote');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertRegExp('/.../', $commandTester->getDisplay());

    }
}
