<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AccessCodes
 *
 * @ORM\Table(
 *      name="access_codes",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"access_code", "code_at"})
 *      }
 * )
 * @UniqueEntity(fields={"accessCode", "codeAt"})
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\AccessCodesRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class AccessCodes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="access_code", type="string")
     */
    private $accessCode;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="code_at", type="date")
     */
    private $codeAt;

    public function __construct()
    {
        $this->accessCode = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set accessCode
     *
     * @param string $accessCode
     * @return AccessCodes
     */
    public function setAccessCode($accessCode)
    {
        $this->accessCode = $accessCode;

        return $this;
    }

    /**
     * Get accessCode
     *
     * @return string
     */
    public function getAccessCode()
    {
        return $this->accessCode;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return AccessCodes
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set userId
     *
     * @param \Application\Sonata\UserBundle\Entity\User $userId
     * @return AccessCodes
     */
    public function setUserId(\Application\Sonata\UserBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDateValue()
    {
        $this->createdAt = $this->codeAt = new \DateTime();
    }

    /**
     * Set codeAt
     *
     * @param \DateTime $codeAt
     * @return AccessCodes
     */
    public function setCodeAt($codeAt)
    {
        $this->codeAt = $codeAt;

        return $this;
    }

    /**
     * Get codeAt
     *
     * @return \DateTime 
     */
    public function getCodeAt()
    {
        return $this->codeAt;
    }
}
