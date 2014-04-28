<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.04.14
 * Time: 12:36
 */

namespace Realtor\DictionaryBundle\Command;

use Application\Sonata\UserBundle\Entity\User;
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
        $em = $this->getContainer()->get('doctrine')->getManager();
        $userManager = $this->getContainer()->get('manager.user');

        $users = $userManager->loadUsers();

        $progress = new ProgressHelper();

        $progress->start($output, count($users));
        if (count($users) > 1) {
            $progress->display();
        }

        $employees = [];

        foreach($users as $user){
            if(empty($user['login']) || empty($user['passsha1'])){
                $user['login'] = strtolower(
                    $user['user_name'].
                    $user['user_second_name'].
                    $user['user_last_name'].
                    '_'.
                    $user['id_user']
                );
                $user['passsha1'] = sha1($user['login']);
            }

            if($user['id_manager'] == 0){
                $employees['head'][] = $user;
            }
            else{
                $employees['employee'][] = $user;
            }
        }

        foreach($employees['head'] as $employee){
            $branch = $em->getRepository('DictionaryBundle:Branches')
                ->findOneBy(['outerId' => $employee['id_office']]);

            if(!$branch){
                continue;
            }

            $userEntity = $em->getRepository('Application\\Sonata\\UserBundle\\Entity\\User')
                ->findOneBy(['outerId' => $employee['id_user']]);

            if(!$userEntity){
                $userEntity = new User();
            }

            if(empty($employee['sys_user_email'])){
                $employee['sys_user_email'] = $employee['login'].'@tech_mail.ru';
            }

            $userEntity->setOuterId($employee['id_user']);
            $userEntity->setUsername($employee['login']);
            $userEntity->setUsernameCanonical($employee['login']);
            $userEntity->setPassword($employee['passsha1']);
            $userEntity->setEmail($employee['sys_user_email']);
            $userEntity->setEmailCanonical($employee['sys_user_email']);
            $userEntity->setPhone($employee['user_phone']);
            $userEntity->setBranch($branch);

            if(!empty($employee['id_office_in']) && (integer)$employee['id_office_in'] > 0){
                $inBranch = $em->getRepository('DictionaryBundle:Branches')
                    ->findOneBy(['outerId' => $employee['id_office_in']]);

                if($inBranch){
                    $userEntity->setInBranch($inBranch);
                }
            }

            if(!empty($employee['officephone'])){
                $userEntity->setOfficePhone($employee['officephone']);
            }

            $userEntity->setMayRedirectCall(((integer)$employee['maytrans'] == 1) ? true : false);
            $userEntity->setInOffice(((integer)$employee['in_office'] == 1) ? true : false);

            $userEntity->setIsFired(((integer)$employee['user_dismiss'] == 1) ? true : false);
            $userEntity->setEnabled(((integer)$employee['user_dismiss'] == 1) ? true : false);

            if(!empty($employee['user_dismiss_date'])){
                $userEntity->setFiredAt(new \DateTime($employee['user_dismiss_date']));
            }

            $em->persist($userEntity);
            $em->flush();
        }

        //$progress->advance();
        //$progress->finish();
    }
} 