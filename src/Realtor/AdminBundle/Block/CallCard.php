<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 27.04.14
 * Time: 16:04
 */

namespace Realtor\AdminBundle\Block;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CallCard extends BaseBlockService
{
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'period' => false,
                'template' => 'SonataAdminBundle:Core:call_card.html.twig',
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
                'settings'  => $settings
            ],
            $response
        );
    }
} 