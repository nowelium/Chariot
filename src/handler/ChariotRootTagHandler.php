<?php

/**
 * @author nowelium
 */
class ChariotRootTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $rootDocument = $context->getGlobalAttribute('document');
        $rootElement = $rootDocument->createElement('div');

        $xpath = new DOMXPath($rootDocument);
        $xpath->registerNamespace('c', ChariotTagHandler::URN);
        $context->setGlobalAttibute('xpath', $xpath);

        $context->setGlobalAttibute('rootElement', $rootElement);
        foreach($xpath->query('//*[@class="dummy"]') as $dummyNode){
            $dummyNode->parentNode->removeChild($dummyNode);
        }
        $parsed = false;
        foreach($xpath->query('//*[@c:template]') as $child){
            $templateName = $child->getAttributeNodeNS(ChariotTagHandler::URN, 'template')->nodeValue;
            $child->removeAttributeNS(ChariotTagHandler::URN, 'template');

            $className = '';
            if($child->hasAttribute('class')){
                $className = $child->getAttribute('class');
            }
            $child->setAttribute('class', $className . ' ' . $templateName);

            $ctx = $context->createChild();
            $ctx->currentNode = $child;
            $ctx->currentTemplateName = $templateName;
            foreach($repository as $handler){
                $handler->start($ctx, $repository);
            }
            $rootElement->appendChild($child);
            $parsed = true;
        }
        if($parsed){
            // remove all nodes
            $rootDocument->removeChild($rootDocument->firstChild);
            // as root(first) element
            $rootDocument->appendChild($rootElement);
            // remove all chariot urn
            //foreach($rootDocument->getElementsByTagNameNS(ChariotTagHandler::URN, '*') as $node){
            //}
        }
        return $rootDocument;
    }
}

