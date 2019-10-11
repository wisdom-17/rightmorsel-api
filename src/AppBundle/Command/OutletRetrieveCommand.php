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

        // var_dump(count($outlets));
        // var_dump(count($abnormalFormatOutlets));
        // die;
        
        $savedOutletsCount          = 0;
        $deactivatedOutletsCount    = 0;
        $updatedGeodataCount        = 0;
        $alreadyExistsCount         = 0;
        foreach($outlets as $outletDetails){
            $outletName     = $outletDetails['outletName'];
            $outletAddress  = $outletDetails['address'];

            $io->text([
                'Processing: '.$outletName
            ]);

            // save each outlet
            $response = $this->outletTableWriter->insertOutlet( 
                $outletName, 
                $outletAddress['buildingName'], 
                $outletAddress['propertyNumber'], 
                $outletAddress['streetName'], 
                $outletAddress['area'], 
                $outletAddress['town'], 
                $outletDetails['telephoneNumber'], 
                $outletAddress['postcode'],
                $outletDetails['longitude'],
                $outletDetails['latitude'],
                $outletDetails['certificationStatus']
            ); 

            $responseStatusCode = $response->getStatusCode();
            $responseContent    = $response->getContent();

            // if successful, increment count
            if($responseStatusCode === 201){
                $savedOutletsCount++;
                $io->note('New outlet, saved to our system.');
            }elseif($responseStatusCode === 200){
                if($responseContent == 'Deactivated, revoked certification.'){
                    $deactivatedOutletsCount++;
                    $io->note('Outlet certification has been revoked.');
                }elseif($responseContent == 'Geodata updated.'){
                    $updatedGeodataCount++;
                    $io->note('Outlet geodata has been updated.');
                }                
            }elseif($responseStatusCode === 422){
                if($responseContent == 'Outlet exists.'){
                    $alreadyExistsCount++;
                    $io->note('Outlet already exists.');
                }
            }
            $io->newLine(1);
        }

        if($savedOutletsCount > 0){
            $io->success('Successfully saved '.$savedOutletsCount.' NEW outlet(s)');
            $io->newLine(1);
        }

        if($deactivatedOutletsCount > 0){
            $io->success('Successfully DEACTIVATED '.$deactivatedOutletsCount.' outlet(s)');
            $io->newLine(1);
        }

        if($updatedGeodataCount > 0){
            $io->success('Successfully UPDATED geodata for '.$updatedGeodataCount.' outlet(s)');
            $io->newLine(1);
        }

        if($alreadyExistsCount > 0){
            $io->success($alreadyExistsCount.' outlet(s) already exist in the system.');
        }

        // highlight outlets which will not be saved automatically
        if(count($abnormalFormatOutlets) > 0){
            $io->text([
                'The following outlets could not be parsed reliably: ',
                '',
            ]);
        
            foreach ($abnormalFormatOutlets as $outletName => $outletAddress) {
                $io->text([
                    trim($outletName),
                    // regex to replace multiple spaces within text with one space
                    trim(preg_replace('!\s+!', ' ', $outletAddress)),
                    ''
                ]);
            }
        }
    }

}
