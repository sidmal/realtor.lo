<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 14.05.14
 * Time: 23:13
 */

namespace Realtor\AdminBundle\Traits;

use Symfony\Component\Security\Core\SecurityContext;

trait Security
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    /**
     * @param SecurityContext $securityContext
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @return SecurityContext
     */
    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->getSecurityContext()->getToken()->getUser();
    }
} 