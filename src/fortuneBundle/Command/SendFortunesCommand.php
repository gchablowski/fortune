<?php

namespace fortuneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

class SendFortunesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('SendFortunes')
                ->setDescription('Send fortunes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        //get the mail
        $em = $this->getContainer()->get('doctrine')->getManager();
        $emails = $em->getRepository('fortuneBundle:Email')->myFindAllEmails();
        
        if(count($emails) > 0){
        //get fortune
        $fortune = $em->getRepository('fortuneBundle:Quote')->myFindLastQuote();
        

        $dispatcher = $this->getContainer()->get('hip_mandrill.dispatcher');

        $message = new Message();

        $message->setSubject('Daily Fortunes')
                ->setHtml($this->getContainer()->get('templating')->render('fortuneBundle:Default:fortune.html.twig', array('quote' => $fortune->getText(), 'author'=> $fortune->getAuthor())))
        ;

        foreach ($emails as $email) {
            $message->addTo($email["email"]);
        }

        $dispatcher->send($message);
        }else{
            $output->writeln("There no one to send the fortune");
        }

        $output->writeln("Done");
    }

}
