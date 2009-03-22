<?php

/**
 * @author nowelium
 */
class ChariotContextTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $currentNode = $context->currentNode;
        if(null === $currentNode){
            // nop ?
            return;
        }
        $currentValue = $context->currentValue;
        $currentIndex = $context->currentIndex;
        $isEven = $currentIndex % 2 === 0;
        $contextValues = $context->getAttributes();

        $xpath = $context->getGlobalAttribute('xpath');
        foreach($xpath->query('*[@c:value]', $currentNode) as $child){
            $key = $child->getAttributeNodeNS(self::URN, 'value')->nodeValue;
            $value = ChariotTagUtils::lookupVariable($contextValues, $currentValue, $key);
            if(is_array($currentValue)){
                $currentValue['_index'] = $currentIndex;
                $currentValue['_odd'] = !$isEven;
                $currentValue['_even'] = $isEven;
                $child->nodeValue = $value;
            } else if(is_object($currentValue)){
            } else {
                $child->nodeValue = $value;
            }
            $child->removeAttributeNS(self::URN, 'value');
        }
    }
}
