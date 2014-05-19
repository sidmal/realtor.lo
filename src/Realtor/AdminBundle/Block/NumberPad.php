<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 23.04.14
 * Time: 16:58
 */

namespace Realtor\AdminBundle\Block;

use Realtor\AdminBundle\Traits\Security;
use Realtor\CallBundle\Model\CallManager;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class NumberPad extends BaseBlockService
{
    use Security;

    /**
     * @var \Realtor\CallBundle\Model\CallManager
     */
    private $callManager;

    public function setCallManager(CallManager $callManager)
    {
        $this->callManager = $callManager;
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'title'    => 'Номеронабератель',
                'template' => 'SonataAdminBundle:Core:numberpad.html.twig',
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
                    ['title', 'text', ['required' => false]],
                ]
            ]
        );
    }

    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        $errorElement
            ->with('settings.title')
                ->assertNotNull([])
                ->assertNotBlank()
                ->assertMaxLength(['limit' => 50])
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
                'tnf' => $this->callManager->tnfCheck($this->getUser()->getOfficePhone()),
                'dnd' => $this->callManager->dndCheck($this->getUser()->getOfficePhone())
            ],
            $response
        );
    }
} 