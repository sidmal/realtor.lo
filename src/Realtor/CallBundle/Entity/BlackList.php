<?php

namespace Realtor\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlackList
 *
 * @ORM\Table(name="black_list")
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class BlackList
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
     * @ORM\Column(name="phone", type="string", length=128)
     */
    private $phone;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="dial_id", type="string", length=128)
     */
    private $dialId;

    /**
     * @var
     *
     * @ORM\Column(name="is_verify", type="boolean", nullable=true)
     */
    private $isVerify = false;

    /**
     * @ORM\PrePersist
     */
    public function setDateValue()
    {
        $this->createdAt = new \DateTime();
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
     * @return BlackList
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
     * Set reason
     *
     * @param string $reason
     * @return BlackList
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return BlackList
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
     * @return BlackList
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
     * Set dialId
     *
     * @param string $dialId
     * @return BlackList
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

    public function __toString()
    {
        return (string)$this->getPhone();
    }

    /**
     * Set isVerify
     *
     * @param boolean $isVerify
     * @return BlackList
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
}
