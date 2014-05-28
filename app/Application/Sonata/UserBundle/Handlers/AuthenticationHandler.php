<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 27.05.14
 * Time: 2:12
 */

namespace Application\Sonata\UserBundle\Handlers;

use Application\Sonata\UserBundle\Entity\AccessCodes;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Validator\Validator;

class AuthenticationHandler
    implements
        AuthenticationSuccessHandlerInterface,
        AuthenticationFailureHandlerInterface,
        LogoutHandlerInterface
{
    /**
     * @var \Symfony\Component\Security\Http\HttpUtils
     */
    protected $httpUtils;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected $httpKernel;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param HttpUtils $httpUtils
     */
    public function setHttpUtils(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    /**
     * @return HttpUtils
     */
    public function getHttpUtils()
    {
        return $this->httpUtils;
    }

    /**
     * @param HttpKernelInterface $httpKernel
     */
    public function setHttpKernel(HttpKernelInterface $httpKernel)
    {
        $this->httpKernel = $httpKernel;
    }

    /**
     * @return HttpKernelInterface
     */
    public function getHttpKernel()
    {
        return $this->httpKernel;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return \Symfony\Component\Validator\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param $user
     * @return AccessCodes
     */
    protected function getAccessCode($user)
    {
        $accessCode = new AccessCodes();
        $accessCode->setUserId($user)->setAccessCode(str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT));

        if(count($this->getValidator()->validate($accessCode)) > 0){
            $this->getAccessCode($user);
        }

        $this->em->persist($accessCode); $this->em->flush($accessCode);

        return $accessCode;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $response = (new DefaultAuthenticationSuccessHandler($this->getHttpUtils(), $this->getOptions()))
            ->onAuthenticationSuccess($request, $token);

        if($request->isXmlHttpRequest()){
            $response = new JsonResponse(
                [
                    'success' => true,
                    'username' => $token->getUsername(),
                    'access_code' => $this->getAccessCode($token->getUser())->getAccessCode(),
                    'user_id' => $token->getUser()->getId()
                ]
            );
        }

        return $response;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = (new DefaultAuthenticationFailureHandler($this->getHttpKernel(), $this->getHttpUtils(), $this->getOptions(), $this->getLogger()))
            ->onAuthenticationFailure($request, $exception);

        if($request->isXmlHttpRequest()){
            $response = new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }

        return $response;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $user = $token->getUser();
        $user->setUserDutyPhone(null);

        $this->getEntityManager()->persist($user); $this->getEntityManager()->flush($user);
    }
} 