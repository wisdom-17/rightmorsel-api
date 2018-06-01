<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Utils\OutletScraper;

class OutletRetrieveCommand extends ContainerAwareCommand
{
    private $outletScraper;

    public function __construct(OutletScraper $outletScraper)
    {
        $this->outletScraper = $outletScraper;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('outlet:retrieve')
            ->setDescription('Scrapes HMC website and saves outlets to db')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL of the webpage containing the list of outlets')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url            = $input->getArgument('url');
        $outletScraper  = new OutletScraper($url);

        // get outlets
        $outlets            = $outletScraper->scrapeOutlets();
        $unformattedOutlets = $outletScraper->unformattedOutlets;

        // highlight outlets which will not be saved automatically
        if(count($unformattedOutlets) > 0){
            $output->writeln([
                'The following outlets could not be parsed: ',
                '',
            ]);
        }
        
        foreach ($unformattedOutlets as $outletName => $outletAddress) {
            $output->writeln([
                $outletName,
                $outletAddress,
            ]);
        }

        // save using our api
        foreach($outlets as $outletName){
            
        }

        $output->writeln('Command result.');
    }

}
