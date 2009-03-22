<?php

/**
 * @author nowelium
 */
class ChariotExistsTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $xpath = $context->getGlobalAttribute('xpath');
        self::exists($xpath, $context, $repository);
        self::notExists($xpath, $context, $repository);
    }
    protected static function exists(DOMXPath $xpath, ChariotContext $context, ChariotTagRepository $repository){
        $nodes = array();
        if(isset($context->currentNode)){
            $nodes = $xpath->query('*[@c:exists]', $context->currentNode);
        } else {
            $nodes = $xpath->query('//*[@c:exists]');
        }
        foreach($nodes as $child){
            $existsValue = $child->getAttributeNodeNS(self::URN, 'exists')->nodeValue;
            $child->removeAttributeNS(self::URN, 'exists');

            if(!$context->hasAttribute($existsValue)){
                $child->nodeValue = '';
                continue;
            }
            $ctx = $context->createChild();
            $ctx->currentIndex = -1;
            $ctx->currentValue = $context->getAttribute($existsValue);
            $ctx->currentNode = $child;
            
            $repos = clone $repository;
            foreach($repos as $r){
                $r->start($ctx, $repository);
            }
        }
    }
    protected static function notExists(DOMXPath $xpath, ChariotContext $context, ChariotTagRepository $repository){
        $nodes = array();
        if(isset($context->currentNode)){
            $nodes = $xpath->query('*[@c:notExists]', $context->currentNode);
        } else {
            $nodes = $xpath->query('//*[@c:notExists]');
        }
        foreach($nodes as $child){
            $existsValue = $child->getAttributeNodeNS(self::URN, 'notExists')->nodeValue;
            $child->removeAttributeNS(self::URN, 'notExists');

            if($context->hasAttribute($existsValue)){
                $child->nodeValue = '';
                $child->parentNode->removeChild($child);
                continue;
            }
            $ctx = $context->createChild();
            $ctx->currentIndex = -1;
            $ctx->currentValue = null;
            $ctx->currentNode = $child;
            
            $repos = clone $repository;
            foreach($repos as $r){
                $r->start($ctx, $repository);
            }
        }
    }
}

