<?php

namespace Realtor\DictionaryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Branches
 *
 * @ORM\Table(
 *      name="branches",
 *      options={
 *          "comment"="справочник филиалов"
 *      }
 * )
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Branches
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
     * @var integer
     *
     * @ORM\Column(name="outer_id", type="integer", nullable=true)
     */
    private $outerId;

    /**
     * @var string
     *
     * @ORM\Column(name="branch_number", type="string", length=128, nullable=true)
     */
    private $branchNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="city_phone", type="string", length=128, nullable=true)
     */
    private $cityPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="on_duty_agent_phone", type="string", length=128, nullable=true)
     */
    private $onDutyAgentPhone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default"=true})
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->isActive = true;
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
     * Set outerId
     *
     * @param integer $outerId
     * @return Branches
     */
    public function setOuterId($outerId)
    {
        $this->outerId = $outerId;

        return $this;
    }

    /**
     * Get outerId
     *
     * @return integer 
     */
    public function getOuterId()
    {
        return $this->outerId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Branches
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Branches
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Branches
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @ORM\PrePersist
     */
    public function setDateValue()
    {
        $this->createdAt = new \DateTime();
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
     * Set branchNumber
     *
     * @param integer $branchNumber
     * @return Branches
     */
    public function setBranchNumber($branchNumber)
    {
        $this->branchNumber = $branchNumber;

        return $this;
    }

    /**
     * Get branchNumber
     *
     * @return integer 
     */
    public function getBranchNumber()
    {
        return $this->branchNumber;
    }

    /**
     * Set cityPhone
     *
     * @param string $cityPhone
     * @return Branches
     */
    public function setCityPhone($cityPhone)
    {
        $this->cityPhone = $cityPhone;

        return $this;
    }

    /**
     * Get cityPhone
     *
     * @return string 
     */
    public function getCityPhone()
    {
        return $this->cityPhone;
    }

    /**
     * Set onDutyAgentPhone
     *
     * @param string $onDutyAgentPhone
     * @return Branches
     */
    public function setOnDutyAgentPhone($onDutyAgentPhone)
    {
        $this->onDutyAgentPhone = $onDutyAgentPhone;

        return $this;
    }

    /**
     * Get onDutyAgentPhone
     *
     * @return string 
     */
    public function getOnDutyAgentPhone()
    {
        return $this->onDutyAgentPhone;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Branches
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
