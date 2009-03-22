<?php

/**
 * @author nowelium
 */
class ChariotForeachTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $xpath = $context->getGlobalAttribute('xpath');
        $nodes = array();
        if(isset($context->currentNode)){
            $nodes = $xpath->query('*[@c:foreach]', $context->currentNode);
        } else {
            $nodes = $xpath->query('//*[@c:foreach]');
        }

        foreach($nodes as $child){
            $foreachValue = $child->getAttributeNodeNS(self::URN, 'foreach')->nodeValue;
            $child->removeAttributeNS(self::URN, 'foreach');
            $children = array();

            $replacement = $context->getAttribute($foreachValue);
            foreach($replacement as $index => $replaceValue){
                foreach($child->childNodes as $c){
                    $clone = $c->cloneNode(true);

                    $ctx = $context->createChild();
                    $ctx->currentNode = $clone;
                    $ctx->currentIndex = $index;
                    $ctx->currentValue = $replaceValue;
                    $repos = clone $repository;
                    foreach($repos as $r){
                        $r->start($ctx, $repository);
                    }
                    $children[] = $clone;
                }
            }

            // remove all children
            $child->nodeValue = '';
            // append new clone nodes
            foreach($children as $_cloneChild){
                $child->appendChild($_cloneChild);
            }
        }
    }
}
