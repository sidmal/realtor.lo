<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 29.03.14
 * Time: 20:06
 */

namespace Realtor\DictionaryBundle\Command;

use Realtor\DictionaryBundle\Exceptions\AdvertisingSourceException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AdvertisingSourceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dictionary:advertising_source:load')
            ->setDescription('Load advertising source dictionary from emls application.')
            ->addOption('rewrite', null, InputOption::VALUE_REQUIRED, 'make full rewriting of the table.', 'no')
            ->addOption('message', null, InputOption::VALUE_REQUIRED, 'write output message.', 'no');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $advertisingSourceManager = $this->getContainer()->get('manager.dictionary.advertising_source');

        try{
            $advertisingSources = $advertisingSourceManager->loadAdvertisingSource();
        }
        catch(AdvertisingSourceException $e){
            return $output->writeln('error on process load advertising source: '.$e->getMessage());
        }

        if($input->getOption('rewrite') == 'yes'){
            $advertisingSourceManager->truncate();
        }

        $progress = new ProgressHelper();

        $progress->start($output, count($advertisingSources));
        if(count($advertisingSources) > 1){
            $progress->display();
        }

        foreach($advertisingSources as $item){
            $advertisingSourceManager->save($item);

            $progress->advance();
        }

        $progress->finish();

        return true;
    }
} 