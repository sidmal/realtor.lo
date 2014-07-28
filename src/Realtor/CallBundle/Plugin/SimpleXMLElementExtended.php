<?php
/**
 * Created by PhpStorm.
 * User: dmitriysinichkin
 * Date: 28.07.14
 * Time: 23:19
 */

namespace Realtor\CallBundle\Plugin;

class SimpleXMLElementExtended extends \SimpleXMLElement
{
    public function addCData($node_name, $cdata_text)
    {
        $node = $this->addChild($node_name);
        $node = dom_import_simplexml($node);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }
} 