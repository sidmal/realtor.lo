<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 05.05.14
 * Time: 22:18
 */

namespace Application\Sonata\UserBundle\Service;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as BaseMessageDigestPasswordEncoder;

class MessageDigestPasswordEncoder extends BaseMessageDigestPasswordEncoder
{
    public function encodePassword($raw, $salt)
    {
        return sha1($raw);
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }
} 