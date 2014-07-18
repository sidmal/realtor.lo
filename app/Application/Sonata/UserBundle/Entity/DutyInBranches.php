<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DutyInBranches
 *
 * @ORM\Table(
 *      name="duty_in_branch",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"duty_date", "duty_time", "branch_id", "duty_agent_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\DutyInBranchRepository")
 * @UniqueEntity(
 *      fields={"dutyDate", "dutyTime", "branchId", "dutyAgent"},
 *      errorPath="branchId",
 *      message="Указанный агент уже назначен дежурным в данном филиале на указанный период времени."
 * )
 *
 * @ORM\HasLifecycleCallbacks()
 */
class DutyInBranches
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
     * @var \DateTime
     *
     * @ORM\Column(name="duty_date", type="date")
     */
    private $dutyDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duty_time", type="time")
     */
    private $dutyTime;

    /**
     * @var \Realtor\DictionaryBundle\Entity\Branches
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\Branches")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     */
    private $branchId;

    /**
     * @var string
     *
     * @ORM\Column(name="duty_phone", type="string", length=255)
     */
    private $dutyPhone;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="duty_agent_id", referencedColumnName="id")
     */
    private $dutyAgent;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dutyTime
     *
     * @param \DateTime $dutyTime
     * @return DutyInBranches
     */
    public function setDutyTime($dutyTime)
    {
        $this->dutyTime = $dutyTime;

        return $this;
    }

    /**
     * Get dutyTime
     *
     * @return \DateTime 
     */
    public function getDutyTime()
    {
        return $this->dutyTime;
    }

    /**
     * Set dutyPhone
     *
     * @param string $dutyPhone
     * @return DutyInBranches
     */
    public function setDutyPhone($dutyPhone)
    {
        $this->dutyPhone = $dutyPhone;

        return $this;
    }

    /**
     * Get dutyPhone
     *
     * @return string 
     */
    public function getDutyPhone()
    {
        return $this->dutyPhone;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return DutyInBranches
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
     * @return DutyInBranches
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
     * Set dutyDate
     *
     * @param \DateTime $dutyDate
     * @return DutyInBranches
     */
    public function setDutyDate($dutyDate)
    {
        $this->dutyDate = $dutyDate;

        return $this;
    }

    /**
     * Get dutyDate
     *
     * @return \DateTime 
     */
    public function getDutyDate()
    {
        return $this->dutyDate;
    }

    /**
     * Set branchId
     *
     * @param \Realtor\DictionaryBundle\Entity\Branches $branchId
     * @return DutyInBranches
     */
    public function setBranchId(\Realtor\DictionaryBundle\Entity\Branches $branchId = null)
    {
        $this->branchId = $branchId;

        return $this;
    }

    /**
     * Get branchId
     *
     * @return \Realtor\DictionaryBundle\Entity\Branches 
     */
    public function getBranchId()
    {
        return $this->branchId;
    }

    /**
     * Set dutyAgent
     *
     * @param \Application\Sonata\UserBundle\Entity\User $dutyAgent
     * @return DutyInBranches
     */
    public function setDutyAgent(\Application\Sonata\UserBundle\Entity\User $dutyAgent = null)
    {
        $this->dutyAgent = $dutyAgent;

        return $this;
    }

    /**
     * Get dutyAgent
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getDutyAgent()
    {
        return $this->dutyAgent;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforeInsert()
    {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function beforeUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
