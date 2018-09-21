<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Utils\OutletScraper;
use AppBundle\Database\OutletTableWriter;


class OutletRetrieveCommand extends ContainerAwareCommand
{
    private $outletScraper;
    private $outletTableWriter;

    public function __construct(OutletScraper $outletScraper, OutletTableWriter $outletTableWriter)
    {
        $this->outletScraper        = $outletScraper;
        $this->outletTableWriter    = $outletTableWriter;

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

        $io = new SymfonyStyle($input, $output);
    
        $url        = $input->getArgument('url');
        $outlets    = $this->outletScraper->scrapeOutlets($url); // get outlets

        $abnormalFormatOutlets = $this->outletScraper->abnormalFormatOutlets;

        // highlight outlets which will not be saved automatically
        if(count($abnormalFormatOutlets) > 0){
            $io->text([
                'The following outlets could not be parsed reliably: ',
                '',
            ]);

            foreach ($abnormalFormatOutlets as $outletName => $outletAddress) {
                $io->text([
                    $outletName,
                    $outletAddress,
                ]);
            }
        }
        
        $savedOutletsCount          = 0;
        $deactivatedOutletsCount    = 0;
        foreach($outlets as $outletDetails){
            $outletName     = $outletDetails['outletName'];
            $outletAddress  = $outletDetails['outletAddress'];

            $io->text([
                'Processing: '.$outletName
            ]);

            // $response = $this->outletTableWriter->insertOutlet( // save each outlet
            $response = $this->outletTableWriter->insertOutlet( // save each outlet
                $outletName, 
                $outletAddress['buildingName'], 
                $outletAddress['propertyNumber'], 
                $outletAddress['streetName'], 
                $outletAddress['area'], 
                $outletAddress['town'], 
                $outletAddress['contactNumber'], 
                $outletAddress['postcode'],
                $outletDetails['longitude'],
                $outletDetails['latitude'],
                $outletDetails['certificationStatus']
            ); 

            $responseStatusCode = $response->getStatusCode();

            // if successful, increment count
            if($responseStatusCode === 201){
                $savedOutletsCount++;
            }elseif($responseStatusCode === 200){
                $deactivatedOutletsCount++;
                // todo: print warning line saying outlet certification has been revoked
            }else{
                $io->text('<error>Oulet could not be saved because: '.$response->getContent().'</>');
            }
            $io->newLine(1);
        }

        $io->success('Successfully saved '.$savedOutletsCount.' outlets');
        $io->newLine(1);
        $io->success('Successfully deactivated '.$deactivatedOutletsCount.' outlets');
    }

}
