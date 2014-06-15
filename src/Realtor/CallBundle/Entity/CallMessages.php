<?php

namespace Realtor\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CallMessages
 *
 * @ORM\Table(name="call_messages")
 * @ORM\Entity
 *
 * @ORM\HasLifecycleCallbacks()
 */
class CallMessages
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
     * @var \Realtor\CallBundle\Entity\CallParams
     *
     * @ORM\ManyToOne(targetEntity="Realtor\CallBundle\Entity\CallParams", inversedBy="message")
     * @ORM\JoinColumn(name="call_params_id", referencedColumnName="id")
     */
    private $callParamId;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
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
     * Set message
     *
     * @param string $message
     * @return CallMessages
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return CallMessages
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
     * Set callParamId
     *
     * @param \Realtor\CallBundle\Entity\CallParams $callParamId
     * @return CallMessages
     */
    public function setCallParamId(\Realtor\CallBundle\Entity\CallParams $callParamId = null)
    {
        $this->callParamId = $callParamId;

        return $this;
    }

    /**
     * Get callParamId
     *
     * @return \Realtor\CallBundle\Entity\CallParams 
     */
    public function getCallParamId()
    {
        return $this->callParamId;
    }
}
