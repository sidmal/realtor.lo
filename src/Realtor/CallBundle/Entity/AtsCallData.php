<?php

namespace Realtor\CallBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AtsCallData
 *
 * @ORM\Table(
 *      name="ats_call_data",
 *      indexes={
 *          @ORM\index(columns={"nrec"}),
 *          @ORM\index(columns={"clid"}),
 *          @ORM\index(columns={"src"}),
 *          @ORM\index(columns={"dst"}),
 *          @ORM\index(columns={"disposition"}),
 *          @ORM\index(columns={"call_date", "call_time"}),
 *      }
 * )
 * @ORM\Entity(repositoryClass="Realtor\CallBundle\Entity\Repository\AtsCallsDataRepository")
 */
class AtsCallData
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
     * @ORM\Column(name="nrec", type="bigint", unique=true)
     */
    private $nrec;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="call_date", type="date")
     */
    private $callDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="call_time", type="time")
     */
    private $callTime;

    /**
     * @var string
     *
     * @ORM\Column(name="clid", type="string", length=255)
     */
    private $clid;

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=128)
     */
    private $src;

    /**
     * @var string
     *
     * @ORM\Column(name="dst", type="string", length=128)
     */
    private $dst;

    /**
     * @var string
     *
     * @ORM\Column(name="dcontext", type="string", length=128)
     */
    private $dcontext;

    /**
     * @var string
     *
     * @ORM\Column(name="channel", type="string", length=255)
     */
    private $channel;

    /**
     * @var string
     *
     * @ORM\Column(name="dstchannel", type="string", length=255)
     */
    private $dstchannel;

    /**
     * @var string
     *
     * @ORM\Column(name="lastapp", type="string", length=128)
     */
    private $lastapp;

    /**
     * @var string
     *
     * @ORM\Column(name="lastdata", type="string", length=255)
     */
    private $lastdata;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * @var integer
     *
     * @ORM\Column(name="billsec", type="integer")
     */
    private $billsec;

    /**
     * @var string
     *
     * @ORM\Column(name="disposition", type="string", length=128)
     */
    private $disposition;

    /**
     * @var integer
     *
     * @ORM\Column(name="amaflags", type="integer")
     */
    private $amaflags;

    /**
     * @var string
     *
     * @ORM\Column(name="accountcode", type="string", length=128)
     */
    private $accountcode;

    /**
     * @var string
     *
     * @ORM\Column(name="uniqueid", type="string", length=255)
     */
    private $uniqueid;

    /**
     * @var string
     *
     * @ORM\Column(name="userfield", type="string", length=255)
     */
    private $userfield;

    /**
     * @var string
     *
     * @ORM\Column(name="x_tag", type="string", length=255, nullable=true)
     */
    private $xTag;

    /**
     * @var string
     *
     * @ORM\Column(name="x_cid", type="string", length=128, nullable=true)
     */
    private $xCid;

    /**
     * @var string
     *
     * @ORM\Column(name="x_did", type="string", length=128, nullable=true)
     */
    private $xDid;

    /**
     * @var string
     *
     * @ORM\Column(name="x_dialed", type="string", length=128, nullable=true)
     */
    private $xDialed;

    /**
     * @var string
     *
     * @ORM\Column(name="x_spec", type="string", length=128, nullable=true)
     */
    private $xSpec;

    /**
     * @var string
     *
     * @ORM\Column(name="x_insecure", type="string", length=255, nullable=true)
     */
    private $xInsecure;

    /**
     * @var string
     *
     * @ORM\Column(name="x_result", type="string", length=255, nullable=true)
     */
    private $xResult;

    /**
     * @var string
     *
     * @ORM\Column(name="x_record", type="string", length=255, nullable=true)
     */
    private $xRecord;

    /**
     * @var string
     *
     * @ORM\Column(name="x_domain", type="string", length=255, nullable=true)
     */
    private $xDomain;

    /**
     * @var string
     *
     * @ORM\Column(name="linkedid", type="string", length=255)
     */
    private $linkedid;

    /**
     * @ORM\ManyToOne(targetEntity="Realtor\CallBundle\Entity\Call")
     * @ORM\JoinColumn(name="call_id", referencedColumnName="id")
     */
    private $callByApplication;


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
     * Set nrec
     *
     * @param integer $nrec
     * @return AtsCallData
     */
    public function setNrec($nrec)
    {
        $this->nrec = $nrec;

        return $this;
    }

    /**
     * Get nrec
     *
     * @return integer 
     */
    public function getNrec()
    {
        return $this->nrec;
    }

    /**
     * Set callDate
     *
     * @param \DateTime $callDate
     * @return AtsCallData
     */
    public function setCallDate($callDate)
    {
        $this->callDate = $callDate;

        return $this;
    }

    /**
     * Get callDate
     *
     * @return \DateTime 
     */
    public function getCallDate()
    {
        return $this->callDate;
    }

    /**
     * Set callTime
     *
     * @param \DateTime $callTime
     * @return AtsCallData
     */
    public function setCallTime($callTime)
    {
        $this->callTime = $callTime;

        return $this;
    }

    /**
     * Get callTime
     *
     * @return \DateTime 
     */
    public function getCallTime()
    {
        return $this->callTime;
    }

    /**
     * Set clid
     *
     * @param string $clid
     * @return AtsCallData
     */
    public function setClid($clid)
    {
        $this->clid = $clid;

        return $this;
    }

    /**
     * Get clid
     *
     * @return string 
     */
    public function getClid()
    {
        return $this->clid;
    }

    /**
     * Set src
     *
     * @param string $src
     * @return AtsCallData
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return string 
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set dst
     *
     * @param string $dst
     * @return AtsCallData
     */
    public function setDst($dst)
    {
        $this->dst = $dst;

        return $this;
    }

    /**
     * Get dst
     *
     * @return string 
     */
    public function getDst()
    {
        return $this->dst;
    }

    /**
     * Set dcontext
     *
     * @param string $dcontext
     * @return AtsCallData
     */
    public function setDcontext($dcontext)
    {
        $this->dcontext = $dcontext;

        return $this;
    }

    /**
     * Get dcontext
     *
     * @return string 
     */
    public function getDcontext()
    {
        return $this->dcontext;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return AtsCallData
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string 
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set dstchannel
     *
     * @param string $dstchannel
     * @return AtsCallData
     */
    public function setDstchannel($dstchannel)
    {
        $this->dstchannel = $dstchannel;

        return $this;
    }

    /**
     * Get dstchannel
     *
     * @return string 
     */
    public function getDstchannel()
    {
        return $this->dstchannel;
    }

    /**
     * Set lastapp
     *
     * @param string $lastapp
     * @return AtsCallData
     */
    public function setLastapp($lastapp)
    {
        $this->lastapp = $lastapp;

        return $this;
    }

    /**
     * Get lastapp
     *
     * @return string 
     */
    public function getLastapp()
    {
        return $this->lastapp;
    }

    /**
     * Set lastdata
     *
     * @param string $lastdata
     * @return AtsCallData
     */
    public function setLastdata($lastdata)
    {
        $this->lastdata = $lastdata;

        return $this;
    }

    /**
     * Get lastdata
     *
     * @return string 
     */
    public function getLastdata()
    {
        return $this->lastdata;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return AtsCallData
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set billsec
     *
     * @param integer $billsec
     * @return AtsCallData
     */
    public function setBillsec($billsec)
    {
        $this->billsec = $billsec;

        return $this;
    }

    /**
     * Get billsec
     *
     * @return integer 
     */
    public function getBillsec()
    {
        return $this->billsec;
    }

    /**
     * Set disposition
     *
     * @param string $disposition
     * @return AtsCallData
     */
    public function setDisposition($disposition)
    {
        $this->disposition = $disposition;

        return $this;
    }

    /**
     * Get disposition
     *
     * @return string 
     */
    public function getDisposition()
    {
        return $this->disposition;
    }

    /**
     * Set amaflags
     *
     * @param integer $amaflags
     * @return AtsCallData
     */
    public function setAmaflags($amaflags)
    {
        $this->amaflags = $amaflags;

        return $this;
    }

    /**
     * Get amaflags
     *
     * @return integer 
     */
    public function getAmaflags()
    {
        return $this->amaflags;
    }

    /**
     * Set accountcode
     *
     * @param string $accountcode
     * @return AtsCallData
     */
    public function setAccountcode($accountcode)
    {
        $this->accountcode = $accountcode;

        return $this;
    }

    /**
     * Get accountcode
     *
     * @return string 
     */
    public function getAccountcode()
    {
        return $this->accountcode;
    }

    /**
     * Set uniqueid
     *
     * @param string $uniqueid
     * @return AtsCallData
     */
    public function setUniqueid($uniqueid)
    {
        $this->uniqueid = $uniqueid;

        return $this;
    }

    /**
     * Get uniqueid
     *
     * @return string 
     */
    public function getUniqueid()
    {
        return $this->uniqueid;
    }

    /**
     * Set userfield
     *
     * @param string $userfield
     * @return AtsCallData
     */
    public function setUserfield($userfield)
    {
        $this->userfield = $userfield;

        return $this;
    }

    /**
     * Get userfield
     *
     * @return string 
     */
    public function getUserfield()
    {
        return $this->userfield;
    }

    /**
     * Set xTag
     *
     * @param string $xTag
     * @return AtsCallData
     */
    public function setXTag($xTag)
    {
        $this->xTag = $xTag;

        return $this;
    }

    /**
     * Get xTag
     *
     * @return string 
     */
    public function getXTag()
    {
        return $this->xTag;
    }

    /**
     * Set xCid
     *
     * @param string $xCid
     * @return AtsCallData
     */
    public function setXCid($xCid)
    {
        $this->xCid = $xCid;

        return $this;
    }

    /**
     * Get xCid
     *
     * @return string 
     */
    public function getXCid()
    {
        return $this->xCid;
    }

    /**
     * Set xDid
     *
     * @param string $xDid
     * @return AtsCallData
     */
    public function setXDid($xDid)
    {
        $this->xDid = $xDid;

        return $this;
    }

    /**
     * Get xDid
     *
     * @return string 
     */
    public function getXDid()
    {
        return $this->xDid;
    }

    /**
     * Set xDialed
     *
     * @param string $xDialed
     * @return AtsCallData
     */
    public function setXDialed($xDialed)
    {
        $this->xDialed = $xDialed;

        return $this;
    }

    /**
     * Get xDialed
     *
     * @return string 
     */
    public function getXDialed()
    {
        return $this->xDialed;
    }

    /**
     * Set xSpec
     *
     * @param string $xSpec
     * @return AtsCallData
     */
    public function setXSpec($xSpec)
    {
        $this->xSpec = $xSpec;

        return $this;
    }

    /**
     * Get xSpec
     *
     * @return string 
     */
    public function getXSpec()
    {
        return $this->xSpec;
    }

    /**
     * Set xInsecure
     *
     * @param string $xInsecure
     * @return AtsCallData
     */
    public function setXInsecure($xInsecure)
    {
        $this->xInsecure = $xInsecure;

        return $this;
    }

    /**
     * Get xInsecure
     *
     * @return string 
     */
    public function getXInsecure()
    {
        return $this->xInsecure;
    }

    /**
     * Set xResult
     *
     * @param string $xResult
     * @return AtsCallData
     */
    public function setXResult($xResult)
    {
        $this->xResult = $xResult;

        return $this;
    }

    /**
     * Get xResult
     *
     * @return string 
     */
    public function getXResult()
    {
        return $this->xResult;
    }

    /**
     * Set xRecord
     *
     * @param string $xRecord
     * @return AtsCallData
     */
    public function setXRecord($xRecord)
    {
        $this->xRecord = $xRecord;

        return $this;
    }

    /**
     * Get xRecord
     *
     * @return string 
     */
    public function getXRecord()
    {
        return $this->xRecord;
    }

    /**
     * Set xDomain
     *
     * @param string $xDomain
     * @return AtsCallData
     */
    public function setXDomain($xDomain)
    {
        $this->xDomain = $xDomain;

        return $this;
    }

    /**
     * Get xDomain
     *
     * @return string 
     */
    public function getXDomain()
    {
        return $this->xDomain;
    }

    /**
     * Set linkedid
     *
     * @param string $linkedid
     * @return AtsCallData
     */
    public function setLinkedid($linkedid)
    {
        $this->linkedid = $linkedid;

        return $this;
    }

    /**
     * Get linkedid
     *
     * @return string 
     */
    public function getLinkedid()
    {
        return $this->linkedid;
    }

    /**
     * Set callByApplication
     *
     * @param \Realtor\CallBundle\Entity\Call $callByApplication
     * @return AtsCallData
     */
    public function setCallByApplication(\Realtor\CallBundle\Entity\Call $callByApplication = null)
    {
        $this->callByApplication = $callByApplication;

        return $this;
    }

    /**
     * Get callByApplication
     *
     * @return \Realtor\CallBundle\Entity\Call 
     */
    public function getCallByApplication()
    {
        return $this->callByApplication;
    }
}
