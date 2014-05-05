<?php

namespace Realtor\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Call
 *
 * @ORM\Table(name="call")
 * @ORM\Entity(repositoryClass="Realtor\CallBundle\Entity\Repository\CallRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Call
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
     * @var string
     *
     * @ORM\Column(name="linked_id", type="string", length=255, nullable=true)
     */
    private $linkedId;

    /**
     * @var string
     *
     * @ORM\Column(name="internal_id", type="string", length=255, nullable=true)
     */
    private $internalId;

    /**
     * @var string
     *
     * @ORM\Column(name="ats_call_id", type="string", length=255, nullable=true)
     */
    private $atsCallId;

    /**
     * 0 - исходящий вызов
     * 1 - входящий вызов
     *
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="from_phone", type="string", length=128)
     */
    private $fromPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="to_phone", type="string", length=128)
     */
    private $toPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="call_action", type="string", length=128)
     */
    private $callAction;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="event_at", type="datetime")
     */
    private $eventAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \Realtor\CallBundle\Entity\CallParams
     *
     * @ORM\OneToOne(targetEntity="Realtor\CallBundle\Entity\CallParams", mappedBy="callId", cascade={"persist"}, orphanRemoval=true)
     **/
    private $params = null;


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
     * Set atsCallId
     *
     * @param string $atsCallId
     * @return Call
     */
    public function setAtsCallId($atsCallId)
    {
        $this->atsCallId = $atsCallId;

        return $this;
    }

    /**
     * Get atsCallId
     *
     * @return string
     */
    public function getAtsCallId()
    {
        return $this->atsCallId;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Call
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set fromPhone
     *
     * @param string $fromPhone
     * @return Call
     */
    public function setFromPhone($fromPhone)
    {
        $this->fromPhone = $fromPhone;

        return $this;
    }

    /**
     * Get fromPhone
     *
     * @return string 
     */
    public function getFromPhone()
    {
        return $this->fromPhone;
    }

    /**
     * Set toPhone
     *
     * @param string $toPhone
     * @return Call
     */
    public function setToPhone($toPhone)
    {
        $this->toPhone = $toPhone;

        return $this;
    }

    /**
     * Get toPhone
     *
     * @return string 
     */
    public function getToPhone()
    {
        return $this->toPhone;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Call
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
     * @ORM\PrePersist
     */
    public function setDateValue()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Set callAction
     *
     * @param string $callAction
     * @return Call
     */
    public function setCallAction($callAction)
    {
        $this->callAction = $callAction;

        return $this;
    }

    /**
     * Get callAction
     *
     * @return string 
     */
    public function getCallAction()
    {
        return $this->callAction;
    }

    /**
     * Set internalId
     *
     * @param string $internalId
     * @return Call
     */
    public function setInternalId($internalId)
    {
        $this->internalId = $internalId;

        return $this;
    }

    /**
     * Get internalId
     *
     * @return string 
     */
    public function getInternalId()
    {
        return $this->internalId;
    }

    /**
     * Set eventAt
     *
     * @param \DateTime $eventAt
     * @return Call
     */
    public function setEventAt($eventAt)
    {
        $this->eventAt = $eventAt;

        return $this;
    }

    /**
     * Get eventAt
     *
     * @return \DateTime 
     */
    public function getEventAt()
    {
        return $this->eventAt;
    }

    /**
     * Set linkedId
     *
     * @param string $linkedId
     * @return Call
     */
    public function setLinkedId($linkedId)
    {
        $this->linkedId = $linkedId;

        return $this;
    }

    /**
     * Get linkedId
     *
     * @return string 
     */
    public function getLinkedId()
    {
        return $this->linkedId;
    }

    /**
     * Set params
     *
     * @param \Realtor\CallBundle\Entity\CallParams $params
     * @return Call
     */
    public function setParams(\Realtor\CallBundle\Entity\CallParams $params = null)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return \Realtor\CallBundle\Entity\CallParams 
     */
    public function getParams()
    {
        return $this->params;
    }
}
