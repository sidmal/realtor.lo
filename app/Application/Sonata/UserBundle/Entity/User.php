<?php
namespace Application\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class User
 * @package Application\Sonata\UserBundle\Entity
 *
 * @ORM\Table(name="fos_user_user")
 * @ORM\Entity()
 */
class User extends BaseUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(
     *      name="outer_id",
     *      type="integer",
     *      options={
     *          "default": 0,
     *          "comment": "идентификатор сотрудника на стороне emls"
     *      },
     *      unique=true,
     *      nullable=true
     * )
     */
    protected $outerId;

    /**
     * филиал к которому приписан сотрудник
     *
     * @var \Realtor\DictionaryBundle\Entity\Branches
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\Branches")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id")
     */
    protected $branch;

    /**
     * филиал в котором в данный момент находится сотрудник
     *
     * @var \Realtor\DictionaryBundle\Entity\Branches
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\Branches")
     * @ORM\JoinColumn(name="in_branch_id", referencedColumnName="id", nullable=true)
     */
    protected $inBranch;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="head_id", referencedColumnName="id", nullable=true)
     */
    protected $head;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_fired", type="boolean", options={"default": false, "comment": "сотрудник уволен"})
     */
    protected $isFired;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fired_at", type="datetime", nullable=true, options={"comment": "дата увольнения"})
     */
    protected $firedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(
     *      name="in_office",
     *      type="boolean",
     *      options={
     *          "default": false,
     *          "comment": "сотрудник находится в оффисе"
     *      }
     * )
     */
    protected $inOffice;

    /**
     * внутренний номер телефона сотрудника в оффисе
     *
     * @var string
     *
     * @ORM\Column(
     *      name="office_phone",
     *      type="string",
     *      length=128,
     *      nullable=true,
     *      options={"comment": "внутренний номер телефона сотрудника в оффисе"}
     * )
     */
    protected $officePhone;

    /**
     * @var boolean
     *
     * @ORM\Column(
     *      name="may_redirect_call",
     *      type="boolean",
     *      options={
     *          "default": false,
     *          "comment": "разрешен перевод звонка на мобильный телефон"
     *      }
     * )
     */
    protected $mayRedirectCall;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

    public function __construct()
    {
        parent::__construct();

        $this->outerId = 0;
        $this->isFired = false;
        $this->mayRedirectCall = false;
        $this->inOffice = false;
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
     * @return User
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
     * Set isFired
     *
     * @param boolean $isFired
     * @return User
     */
    public function setIsFired($isFired)
    {
        $this->isFired = $isFired;

        return $this;
    }

    /**
     * Get isFired
     *
     * @return boolean 
     */
    public function getIsFired()
    {
        return $this->isFired;
    }

    /**
     * Set firedAt
     *
     * @param \DateTime $firedAt
     * @return User
     */
    public function setFiredAt($firedAt)
    {
        $this->firedAt = $firedAt;

        return $this;
    }

    /**
     * Get firedAt
     *
     * @return \DateTime 
     */
    public function getFiredAt()
    {
        return $this->firedAt;
    }

    /**
     * Set inOffice
     *
     * @param boolean $inOffice
     * @return User
     */
    public function setInOffice($inOffice)
    {
        $this->inOffice = $inOffice;

        return $this;
    }

    /**
     * Get inOffice
     *
     * @return boolean 
     */
    public function getInOffice()
    {
        return $this->inOffice;
    }

    /**
     * Set officePhone
     *
     * @param string $officePhone
     * @return User
     */
    public function setOfficePhone($officePhone)
    {
        $this->officePhone = $officePhone;

        return $this;
    }

    /**
     * Get officePhone
     *
     * @return string 
     */
    public function getOfficePhone()
    {
        return $this->officePhone;
    }

    /**
     * Set mayRedirectCall
     *
     * @param boolean $mayRedirectCall
     * @return User
     */
    public function setMayRedirectCall($mayRedirectCall)
    {
        $this->mayRedirectCall = $mayRedirectCall;

        return $this;
    }

    /**
     * Get mayRedirectCall
     *
     * @return boolean 
     */
    public function getMayRedirectCall()
    {
        return $this->mayRedirectCall;
    }

    /**
     * Set branch
     *
     * @param \Realtor\DictionaryBundle\Entity\Branches $branch
     * @return User
     */
    public function setBranch(\Realtor\DictionaryBundle\Entity\Branches $branch = null)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \Realtor\DictionaryBundle\Entity\Branches 
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set inBranch
     *
     * @param \Realtor\DictionaryBundle\Entity\Branches $inBranch
     * @return User
     */
    public function setInBranch(\Realtor\DictionaryBundle\Entity\Branches $inBranch = null)
    {
        $this->inBranch = $inBranch;

        return $this;
    }

    /**
     * Get inBranch
     *
     * @return \Realtor\DictionaryBundle\Entity\Branches 
     */
    public function getInBranch()
    {
        return $this->inBranch;
    }

    /**
     * Set head
     *
     * @param \Application\Sonata\UserBundle\Entity\User $head
     * @return User
     */
    public function setHead(\Application\Sonata\UserBundle\Entity\User $head = null)
    {
        $this->head = $head;

        return $this;
    }

    /**
     * Get head
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
