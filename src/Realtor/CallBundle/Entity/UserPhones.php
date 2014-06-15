<?php

namespace Realtor\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPhones
 *
 * @ORM\Table(name="user_phones")
 * @ORM\Entity(repositoryClass="Realtor\CallBundle\Entity\Repository\UserPhonesRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class UserPhones
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
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="appended_user_id", referencedColumnName="id")
     */
    private $appendedUserId;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=128)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_action", type="string", length=128)
     */
    private $phoneAction;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_verify", type="boolean")
     */
    private $isVerify = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="dial_id", type="string", length=128)
     */
    private $dialId;

    public function __construct()
    {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateValue()
    {
        $this->updatedAt = new \DateTime();
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
     * Set phone
     *
     * @param string $phone
     * @return UserPhones
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isVerify
     *
     * @param boolean $isVerify
     * @return UserPhones
     */
    public function setIsVerify($isVerify)
    {
        $this->isVerify = $isVerify;

        return $this;
    }

    /**
     * Get isVerify
     *
     * @return boolean 
     */
    public function getIsVerify()
    {
        return $this->isVerify;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserPhones
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return UserPhones
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set userId
     *
     * @param \Application\Sonata\UserBundle\Entity\User $userId
     * @return UserPhones
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
     * Set phoneAction
     *
     * @param string $phoneAction
     * @return UserPhones
     */
    public function setPhoneAction($phoneAction)
    {
        $this->phoneAction = $phoneAction;

        return $this;
    }

    /**
     * Get phoneAction
     *
     * @return string 
     */
    public function getPhoneAction()
    {
        return $this->phoneAction;
    }

    /**
     * Set appendedUserId
     *
     * @param \Application\Sonata\UserBundle\Entity\User $appendedUserId
     * @return UserPhones
     */
    public function setAppendedUserId(\Application\Sonata\UserBundle\Entity\User $appendedUserId = null)
    {
        $this->appendedUserId = $appendedUserId;

        return $this;
    }

    /**
     * Get appendedUserId
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getAppendedUserId()
    {
        return $this->appendedUserId;
    }

    /**
     * Set dialId
     *
     * @param string $dialId
     * @return UserPhones
     */
    public function setDialId($dialId)
    {
        $this->dialId = $dialId;

        return $this;
    }

    /**
     * Get dialId
     *
     * @return string 
     */
    public function getDialId()
    {
        return $this->dialId;
    }
}
