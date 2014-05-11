<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 10.05.14
 * Time: 1:38
 */

namespace Realtor\DictionaryBundle\Command;

use Realtor\DictionaryBundle\Exceptions\BranchException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BranchLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dictionary:branch:load')
            ->setDescription('Load branches dictionary from emls application.')
            ->addOption('rewrite', null, InputOption::VALUE_REQUIRED, 'make full rewriting of the table.', 'yes')
            ->addOption('message', null, InputOption::VALUE_REQUIRED, 'write output message.', 'no');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $branchManager = $this->getContainer()->get('manager.dictionary.branch');

        try{
            $branches = $branchManager->loadBranch();
        }
        catch(BranchException $e){
            return $output->writeln('error on process load branch: '.$e->getMessage());
        }

        if($input->getOption('rewrite') == 'yes'){
            $branchManager->truncate();
        }

        $progress = new ProgressHelper();

        $progress->start($output, count($branches));
        if(count($branches) > 1){
            $progress->display();
        }

        foreach($branches as $branch){
            $branchManager->save($branch);

            $progress->advance();
        }

        $progress->finish();

        return true;
    }
} 