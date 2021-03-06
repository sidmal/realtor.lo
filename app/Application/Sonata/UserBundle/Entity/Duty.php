<?php

namespace Application\Sonata\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Realtor\DictionaryBundle\Entity\Branches;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Duty
 *
 * @ORM\Table(
 *      name="duty",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"branch_id", "duty_start_at", "duty_end_at"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Application\Sonata\UserBundle\Entity\Repository\DutyRepository")
 * @UniqueEntity(
 *      fields={"branchId", "dutyStartAt", "dutyEndAt"},
 *      errorPath="branchId",
 *      message="Для указанного филиала уже назначен дежурный на данный временной период."
 * )
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Duty
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
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     */
    private $manager;

    /**
     * @var \Realtor\DictionaryBundle\Entity\Branches
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\Branches")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     */
    private $branchId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duty_start_at", type="datetime")
     */
    private $dutyStartAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duty_end_at", type="datetime")
     */
    private $dutyEndAt;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=128)
     */
    private $phone;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createAt;

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
     * Set userId
     *
     * @param \Application\Sonata\UserBundle\Entity\User $userId
     * @return Duty
     */
    public function setUserId(User $userId)
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
     * Set branchId
     *
     * @param \Realtor\DictionaryBundle\Entity\Branches $branchId
     * @return Duty
     */
    public function setBranchId(Branches $branchId)
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
     * Set dutyStartAt
     *
     * @param \DateTime $dutyStartAt
     * @return Duty
     */
    public function setDutyStartAt($dutyStartAt)
    {
        $this->dutyStartAt = $dutyStartAt;

        return $this;
    }

    /**
     * Get dutyStartAt
     *
     * @return \DateTime 
     */
    public function getDutyStartAt()
    {
        return $this->dutyStartAt;
    }

    /**
     * Set dutyEndAt
     *
     * @param \DateTime $dutyEndAt
     * @return Duty
     */
    public function setDutyEndAt($dutyEndAt)
    {
        $this->dutyEndAt = $dutyEndAt;

        return $this;
    }

    /**
     * Get dutyEndAt
     *
     * @return \DateTime 
     */
    public function getDutyEndAt()
    {
        return $this->dutyEndAt;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Duty
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
     * Set createAt
     *
     * @param \DateTime $createAt
     * @return Duty
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;

        return $this;
    }

    /**
     * Get createAt
     *
     * @return \DateTime 
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Duty
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
     * @ORM\PrePersist
     */
    public function setDateValue()
    {
        $this->createAt = $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateValue()
    {
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return ($this->getId()) ? (string)$this->getId() : 'Новое дежурство';
    }

    /**
     * Set manager
     *
     * @param \Application\Sonata\UserBundle\Entity\User $manager
     * @return Duty
     */
    public function setManager(\Application\Sonata\UserBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getManager()
    {
        return $this->manager;
    }
}
