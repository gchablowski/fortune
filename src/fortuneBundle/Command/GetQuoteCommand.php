<?php

namespace fortuneBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

class GetQuoteCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('fortune:getQuote')
                ->setDescription('get a quote from a service')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        //call api quote with guzzle
        $oClient = $this->getContainer()->get('guzzle.client.api_qod');
        $oResponse = $oClient->get('api-3.0.json');
        //recuperate the quote on json
        $sQuoTeresponse = json_decode($oResponse->getBody());

        $command = $this->getApplication()->find('setQuote');

        $arguments = array(
            'command' => 'setQuote',
            'text' => $sQuoTeresponse->quote,
            'author' => $sQuoTeresponse->author,
        );

        $setQuoteInput = new ArrayInput($arguments);
        $returnCode = $command->run($setQuoteInput, $output);
    }

}
