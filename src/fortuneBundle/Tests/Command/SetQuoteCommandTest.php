<?php

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use fortuneBundle\Command\GetQuoteCommand;
use fortuneBundle\Command\SetQuoteCommand;

class SetQuoteCommandTest extends KernelTestCase {

    protected $text = "Lorem ipsum";
    protected $author = "sit amet";

    protected function CreateApp() {
        // create kernel to have service
        $kernel = $this->createKernel();
        $kernel->boot();

        return $kernel;
    }

    protected function CommandExe() {
        $kernel = $this->CreateApp();
        // start teh command line with argument
        $application = new Application($kernel);
        $application->add(new GetQuoteCommand());
        $application->add(new SetQuoteCommand());

        $command = $application->find('fortune:setQuote');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
                array('command' => $command->getName(),
                    'text' => $this->text,
                    'author' => $this->author
                )
        );

        $this->assertRegExp('/.../', $commandTester->getDisplay());

        return $commandTester->getDisplay();
    }

    public function testInsertQuote() {
        $kernel = $this->CreateApp();
        $commandDisplay = $this->CommandExe();

        //verify that the insert be done
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $getInsert = $this->em->getRepository('fortuneBundle:Quote')->findBy(array('text' => $this->text, 'author' => $this->author));

        $this->assertCount(1, $getInsert);
        
        $this->assertRegExp('/^Done/', $commandDisplay);
        
        //test a second insert
        $commandDisplay = $this->CommandExe();

        $this->assertRegExp('/^Errors/', $commandDisplay);

        //suppress mock on database
        $this->em->remove($getInsert[0]);
        $this->em->flush();
    }

}
