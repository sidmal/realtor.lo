<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 27.04.14
 * Time: 16:04
 */

namespace Realtor\AdminBundle\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class CallCard extends BaseBlockService
{
    protected $em;

    protected $callDialUpEvent;

    protected $callDialDownEvent;

    public function __construct($name, EngineInterface $templating, EntityManager $em)
    {
        parent::__construct($name, $templating);

        $this->em = $em;
    }

    public function setCallDialUpEvent($callDialUpEvent)
    {
        $this->callDialUpEvent = $callDialUpEvent;

        return $this;
    }

    public function getCallDialUpEvent()
    {
        if(!$this->callDialUpEvent){
            throw new \Exception('"call.dial_up.event" parameter not set.');
        }

        return $this->callDialUpEvent;
    }

    public function setCallDialDownEvent($callDialDownEvent)
    {
        $this->callDialDownEvent = $callDialDownEvent;

        return $this;
    }

    public function getCallDialDownEvent()
    {
        if(!$this->callDialDownEvent){
            throw new \Exception('"call.dial_down.event" parameter not set.');
        }

        return $this->callDialDownEvent;
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'period' => false,
                'template' => 'SonataAdminBundle:Core:call_card_v2.html.twig',
            ]
        );
    }

    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add(
            'settings',
            'sonata_type_immutable_array',
            [
                'keys' => [
                    ['period', 'integer', ['required' => true]],
                ]
            ]
        );
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings.period')
                ->assertNotNull([])
                ->assertNotBlank()
            ->end();
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();

        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block'     => $blockContext->getBlock(),
                'settings'  => $settings,
                'advertising_source' => $this->em->getRepository('DictionaryBundle:AdvertisingSource')->findBy(['isActive' => true]),
                'reason' => $this->em->getRepository('DictionaryBundle:Reason')->findBy(['isActive' => true]),
                'branches' => $this->em->getRepository('DictionaryBundle:Branches')->findBy(['isActive' => true]),
                'call_dial_up_event' => $this->getCallDialUpEvent(),
                'call_dial_down_event' => $this->getCallDialDownEvent(),
            ],
            $response
        );
    }
} 