<?php

namespace fortuneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use fortuneBundle\Entity\Quote;

class GetQuoteCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('getquote')
                ->setDescription('get a quote for today')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        //call api quote with guzzle
        $oClient = $this->getContainer()->get('guzzle.client.api_qod');
        $oResponse = $oClient->get('api-3.0.json');
        //recuperate the quote on json
        $sQuoTeresponse = json_decode($oResponse->getBody());

        // create a new quote object and add it to data base
        $oQuote = new Quote;
        $oQuote->setText($sQuoTeresponse->quote);
        $oQuote->setAuthor($sQuoTeresponse->author);

        $oValidator = $this->getContainer()->get('validator');
        $aErrors = $oValidator->validate($oQuote);

        if (count($aErrors) > 0) {
            //show the error
            $output->writeln("Errors list :");
            for ($i=0; $i < count($aErrors); $i++) {
                $oError = $aErrors[$i];
                $output->writeln(($i+1)."-".$oError->getMessage()."");
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