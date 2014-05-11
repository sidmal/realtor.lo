<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 04.05.14
 * Time: 23:34
 */

namespace Realtor\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class CallParams
 * @package Realtor\CallBundle\Entity
 *
 * @ORM\Table(name="call_params")
 * @ORM\Entity
 */
class CallParams
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Realtor\CallBundle\Entity\Call
     *
     * @ORM\OneToOne(targetEntity="Realtor\CallBundle\Entity\Call", inversedBy="params")
     * @ORM\JoinColumn(name="call_id", referencedColumnName="id")
     */
    private $callId;

    /**
     * @var integer
     *
     * 0 - Звонок от продавца
     * 1 - Звонок от агентства
     * 2 - Звонок от сотрудника
     * 3 - Клиент знает объект
     * 4 - Клиент не знает чего хочет
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $callType;

    /**
     * @var string
     *
     * @ORM\Column(name="caller_name", type="string", length=255)
     */
    private $callerName;

    /**
     * @var \Realtor\DictionaryBundle\Entity\AdvertisingSource
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\AdvertisingSource")
     * @ORM\JoinColumn(name="advertising_source_id", referencedColumnName="id", nullable=true)
     */
    private $advertisingSource;

    /**
     * @var integer
     *
     * @ORM\Column(name="property_id", type="integer", nullable=true)
     */
    private $propertyId;

    /**
     * @var string
     *
     * @ORM\Column(name="property_address", type="text", nullable=true)
     */
    private $propertyAddress;

    /**
     * @var \Application\Sonata\UserBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=true)
     */
    private $propertyAgentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="property_base_id", type="integer", nullable=true)
     */
    private $propertyBaseId;

    /**
     * @var \Realtor\DictionaryBundle\Entity\Reason
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\Reason")
     * @ORM\JoinColumn(name="reason_id", referencedColumnName="id", nullable=true)
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var \Realtor\DictionaryBundle\Entity\Branches
     *
     * @ORM\ManyToOne(targetEntity="Realtor\DictionaryBundle\Entity\Branches")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=true)
     */
    private $branch;

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
     * Set callType
     *
     * @param integer $callType
     * @return CallParams
     */
    public function setCallType($callType)
    {
        $this->callType = $callType;

        return $this;
    }

    /**
     * Get callType
     *
     * @return integer 
     */
    public function getCallType()
    {
        return $this->callType;
    }

    /**
     * Set callerName
     *
     * @param string $callerName
     * @return CallParams
     */
    public function setCallerName($callerName)
    {
        $this->callerName = $callerName;

        return $this;
    }

    /**
     * Get callerName
     *
     * @return string 
     */
    public function getCallerName()
    {
        return $this->callerName;
    }

    /**
     * Set propertyId
     *
     * @param integer $propertyId
     * @return CallParams
     */
    public function setPropertyId($propertyId)
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    /**
     * Get propertyId
     *
     * @return integer 
     */
    public function getPropertyId()
    {
        return $this->propertyId;
    }

    /**
     * Set propertyAddress
     *
     * @param string $propertyAddress
     * @return CallParams
     */
    public function setPropertyAddress($propertyAddress)
    {
        $this->propertyAddress = $propertyAddress;

        return $this;
    }

    /**
     * Get propertyAddress
     *
     * @return string 
     */
    public function getPropertyAddress()
    {
        return $this->propertyAddress;
    }

    /**
     * Set propertyBaseId
     *
     * @param integer $propertyBaseId
     * @return CallParams
     */
    public function setPropertyBaseId($propertyBaseId)
    {
        $this->propertyBaseId = $propertyBaseId;

        return $this;
    }

    /**
     * Get propertyBaseId
     *
     * @return integer 
     */
    public function getPropertyBaseId()
    {
        return $this->propertyBaseId;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return CallParams
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set callId
     *
     * @param \Realtor\CallBundle\Entity\Call $callId
     * @return CallParams
     */
    public function setCallId(\Realtor\CallBundle\Entity\Call $callId = null)
    {
        $this->callId = $callId;

        return $this;
    }

    /**
     * Get callId
     *
     * @return \Realtor\CallBundle\Entity\Call 
     */
    public function getCallId()
    {
        return $this->callId;
    }

    /**
     * Set advertisingSource
     *
     * @param \Realtor\DictionaryBundle\Entity\AdvertisingSource $advertisingSource
     * @return CallParams
     */
    public function setAdvertisingSource(\Realtor\DictionaryBundle\Entity\AdvertisingSource $advertisingSource = null)
    {
        $this->advertisingSource = $advertisingSource;

        return $this;
    }

    /**
     * Get advertisingSource
     *
     * @return \Realtor\DictionaryBundle\Entity\AdvertisingSource 
     */
    public function getAdvertisingSource()
    {
        return $this->advertisingSource;
    }

    /**
     * Set propertyAgentId
     *
     * @param \Application\Sonata\UserBundle\Entity\User $propertyAgentId
     * @return CallParams
     */
    public function setPropertyAgentId(\Application\Sonata\UserBundle\Entity\User $propertyAgentId = null)
    {
        $this->propertyAgentId = $propertyAgentId;

        return $this;
    }

    /**
     * Get propertyAgentId
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getPropertyAgentId()
    {
        return $this->propertyAgentId;
    }

    /**
     * Set reason
     *
     * @param \Realtor\DictionaryBundle\Entity\Reason $reason
     * @return CallParams
     */
    public function setReason(\Realtor\DictionaryBundle\Entity\Reason $reason = null)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return \Realtor\DictionaryBundle\Entity\Reason 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set branch
     *
     * @param \Realtor\DictionaryBundle\Entity\Branches $branch
     * @return CallParams
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
}
