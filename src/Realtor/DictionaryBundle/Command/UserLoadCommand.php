<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.04.14
 * Time: 12:36
 */

namespace Realtor\DictionaryBundle\Command;

use Realtor\DictionaryBundle\Exceptions\UserException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserLoadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('users:load')
            ->setDescription('Load users from emls application.')
            ->addOption('rewrite', null, InputOption::VALUE_REQUIRED, 'make full rewriting of the table.', 'yes')
            ->addOption('message', null, InputOption::VALUE_REQUIRED, 'write output message.', 'no');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('manager.user');

        try{
            $users = $userManager->loadUsers();
        }
        catch(UserException $e){
            return $output->writeln('error on process load user: '.$e->getMessage());
        }

        $progress = $this->getHelperSet()->get('progress');

        $progress->start($output, count($users));
        if(count($users) > 1){
            $progress->display();
        }

        $employees = [];

        foreach($users as $user){
            if($user['id_manager'] == 0){
                $employees['head'][] = $user;
            }
            else{
                $employees['employee'][] = $user;
            }
        }

        $currentUsersLogin = $this->getContainer()->get('doctrine')->getManager()->getRepository('ApplicationSonataUserBundle:User')->getCurrentLogin();
        $currentEmailsLogin = $this->getContainer()->get('doctrine')->getManager()->getRepository('ApplicationSonataUserBundle:User')->getCurrentEmail();

        foreach($employees['head'] as $employee){
            if(in_array($employee['login'], $currentUsersLogin)){
                $employee['login'] = $employee['login'].'_'.md5(uniqid(rand(),1));
            }

            if(in_array($employee['sys_user_email'], $currentEmailsLogin)){
                $employee['sys_user_email'] = $employee['sys_user_email'].'_'.md5(uniqid(rand(),1));
            }

            $userManager->save($employee);

            $progress->advance();
        }

        foreach($employees['employee'] as $employee){
            $userManager->save($employee);

            $progress->advance();
        }

        $progress->finish();
    }
} 