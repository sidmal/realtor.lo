<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.04.14
 * Time: 11:33
 */

namespace Realtor\DictionaryBundle\Model;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\RequestInterface;
use Realtor\DictionaryBundle\Exceptions\UserException;
use Symfony\Component\Validator\Validator;

class UserManager
{
    /**
     * @var \Guzzle\Http\Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    private $validator;

    public function __construct($url, EntityManager $em, Validator\ValidatorInterface $validator)
    {
        if(empty($url)){
            new UserException('url for load users from remote service not set.');
        }

        $this->httpClient = (new HttpClient())->getClient();
        $this->url = $url;
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param \Guzzle\Http\Message\RequestInterface $request
     * @return array
     */
    protected static function formatResponse(RequestInterface $request)
    {
        $response = null;

        try{
            $response = $request->send();
        }
        catch(RequestException $e){
            new UserException('request failed', $e->getCode(), $e);
        }

        $response = $response->json();

        if (!$response) {
            new UserException('response for action load user is not a valid json document.');
        }

        return $response;
    }

    public function loadUserById($userId)
    {
        $request = $this->httpClient->get($this->url.'/'.$userId, ['Accept' => 'application/json']);

        return self::formatResponse($request);
    }

    public function loadUsers()
    {
        $request = $this->httpClient->get($this->url, ['Accept' => 'application/json']);

        return self::formatResponse($request);
    }

    public function save(array $employee)
    {
        if(empty($employee['login'])){
            $employee['login'] = '<login is empty '.$employee['id_user'].'>';
        }

        $user = $this->em->getRepository('Application\\Sonata\\UserBundle\\Entity\\User')->findOneBy(['outerId' => $employee['id_user']]);

        if(!$user){
            $user = new User();
        }

        if(empty($employee['id_office']) || (integer)$employee['id_office'] <= 0){
            return false;
        }

        $branch = $this->em->getRepository('DictionaryBundle:Branches')->findOneBy(['outerId' => $employee['id_office']]);

        if(!$branch){
            return false;
        }

        if(empty($employee['login'])){
            $employee['login'] = '<login is empty '.$employee['id_user'].'>';
        }

        if(empty($employee['sys_user_email'])){
            $employee['sys_user_email'] = '<email is empty '.$employee['id_user'].'>';
        }

        $user->setOuterId($employee['id_user']);
        $user->setUsername($employee['login']);
        $user->setUsernameCanonical($employee['login']);
        $user->setPassword($employee['passsha1']);
        $user->setEmail($employee['sys_user_email']);
        $user->setEmailCanonical($employee['sys_user_email']);
        $user->setPhone($employee['user_phone']);
        $user->setBranch($branch);
        $user->setFirstname($employee['user_name']);
        $user->setLastname($employee['user_last_name']);
        $user->setSecondName(empty($employee['user_second_name']) ? null : $employee['user_second_name']);
        $user->setFio(empty($employee['user_fio']) ? null : $employee['user_fio']);
        $user->setMayRedirectCall(((integer)$employee['maytrans'] == 1) ? true : false);
        $user->setInOffice(((integer)$employee['in_office'] == 1) ? true : false);
        $user->setIsFired(((integer)$employee['user_dismiss'] == 1) ? true : false);
        $user->setEnabled(((integer)$employee['user_dismiss'] == 1) ? false : true);

        if(!empty($employee['id_office_in']) && (integer)$employee['id_office_in'] > 0){
            $inBranch = $this->em->getRepository('DictionaryBundle:Branches')->findOneBy(['outerId' => $employee['id_office_in']]);

            if($inBranch){
                $user->setInBranch($inBranch);
            }
        }

        if(!empty($employee['officephone'])){
            $user->setOfficePhone($employee['officephone']);
        }

        if(!empty($employee['user_dismiss_date'])){
            $user->setFiredAt(new \DateTime($employee['user_dismiss_date']));
        }

        if(!empty($employee['id_role'])){
            $group = $this->em->getRepository('Application\Sonata\UserBundle\Entity\Group')->find($employee['id_role']);

            if($group){
                $user->addGroup($group);
                $user->setRoles($group->getRoles());
            }
        }

        $user->setHead(null);
        if(!empty($employee['id_manager'])){
            $head = $this->em->getRepository('Application\\Sonata\\UserBundle\\Entity\\User')
                ->findOneBy(['outerId' => $employee['id_manager']]);

            if($head){
                $user->setHead($head);
            }
        }

        if(count($violations = $this->validator->validate($user)) > 0){
            $user->setUsername($employee['login'].'_'.md5(uniqid(rand(),1)));
            $user->setUsernameCanonical($employee['login'].'_'.md5(uniqid(rand(),1)));
            $user->setEmail($employee['sys_user_email'].'_'.md5(uniqid(rand(),1)));
            $user->setEmailCanonical($employee['sys_user_email'].'_'.md5(uniqid(rand(),1)));
        }

        try{
            $this->em->persist($user);
            $this->em->flush($user);
        }
        catch(\Exception $e){
            echo $e->getMessage()."\n";
        }

        return $user->getId();
    }
} 