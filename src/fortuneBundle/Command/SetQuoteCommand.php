<?php

namespace fortuneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use fortuneBundle\Entity\Quote;

class SetQuoteCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('setQuote')
                ->setDescription('set a quote')
                ->addArgument(
                        'text', InputArgument::REQUIRED, 'text of the quote'
                )
                ->addArgument(
                        'author', InputArgument::REQUIRED, 'author of the quote'
        );

    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        //get the arguments
        $text = $input->getArgument('text');
        $author = $input->getArgument('author');
        // create a new quote object and add it to data base
        $oQuote = new Quote;
        $oQuote->setText($text);
        $oQuote->setAuthor($author);

        $oValidator = $this->getContainer()->get('validator');
        $aErrors = $oValidator->validate($oQuote);

        if (count($aErrors) > 0) {
            //show the error
            $output->writeln("Errors list :");
            for ($i = 0; $i < count($aErrors); $i++) {
                $oError = $aErrors[$i];
                $output->writeln(($i + 1) . " - " . $oError->getMessage() . "");
            }
        } else {
            //persist the new quote
            $em = $this->getContainer()->get('doctrine')->getManager();
            $em->persist($oQuote);
            $em->flush();

            $output->writeln("Done");
        }
    }

}
