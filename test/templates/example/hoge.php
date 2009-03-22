<?php

class ChariotTagRepository implements Iterator {
    protected $iterator;
    public function __construct(){
        $this->iterator = new ArrayIterator;
    }
    public function set($name, ChariotTagHandler $handler){
        $this->iterator->offsetSet($name, $handler);
    }
    public function current(){
        return $this->iterator->current();
    }
    public function next(){
        return $this->iterator->next();
    }
    public function key(){
        return $this->iterator->key();
    }
    public function rewind(){
        return $this->iterator->rewind();
    }
    public function valid(){
        return $this->iterator->valid();
    }
}

class ChariotContext {
    protected $parent = null;
    protected $holder = array();
    protected $globalAttribute = array();
    protected $attributeHolder = array();
    public function __set($name, $value){
        $this->holder[$name] = $value;
    }
    public function & __get($name){
        return $this->holder[$name];
    }
    public function __isset($name){
        return isset($this->holder[$name]);
    }
    public function setAttribute($name, $value){
        $this->attributeHolder[$name] = $value;
    }
    public function getAttribute($name){
        return $this->attributeHolder[$name];
    }
    public function getAttributes(){
        return $this->attributeHolder;
    }
    public function hasAttribute($name){
        return isset($this->attributeHolder[$name]);
    }
    public function setGlobalAttibute($name, $value){
        $this->globalAttribute[$name] = $value;
    }
    public function getGlobalAttribute($name){
        return $this->globalAttribute[$name];
    }
    public function getParent(){
        return $this->parent;
    }
    public function hasParent(){
        return null !== $this->parent;
    }
    public function createChild(){
        $child = new self;
        $child->parent = $this;
        $child->holder = array();
        $child->attributeHolder = (array) $this->attributeHolder;
        $child->globalAttribute = (array) $this->globalAttribute;
        return $child;
    }
}

interface ChariotTagHandler {
    const URN = 'urn://Chariot';
    public function start(ChariotContext $context, ChariotTagRepository $repository);
}

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
        }
        // remove all nodes
        $rootDocument->removeChild($rootDocument->firstChild);
        // as root(first) element
        $rootDocument->appendChild($rootElement);
        // remove all chariot urn
        //foreach($rootDocument->getElementsByTagNameNS(ChariotTagHandler::URN, '*') as $node){
        //}
        return $rootDocument;
    }
}

class ChariotForeachTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $nodes = array();
        if(isset($context->currentNode)){
            $nodes = $xpath->query('*/*[@c:foreach]', $context->currentNode);
        } else {
            $nodes = $xpath->query('//*[@c:foreach]');
        }

        $xpath = $context->getGlobalAttribute('xpath');
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
class ChariotExistsTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $xpath = $context->getGlobalAttribute('xpath');
        self::exists($xpath, $context, $repository);
        self::notExists($xpath, $context, $repository);
    }
    protected static function exists(DOMXPath $xpath, ChariotContext $context, ChariotTagRepository $repository){
        $nodes = array();
        if(isset($context->currentNode)){
            $nodes = $xpath->query('*/*[@c:exists]', $context->currentNode);
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
            $nodes = $xpath->query('*/*[@c:notExists]', $context->currentNode);
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

class ChariotSymfonyTagHandler implements ChariotTagHandler {
    public function start(ChariotContext $context, ChariotTagRepository $repository){
        $xpath = $context->getGlobalAttribute('xpath');
    }
}

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

abstract class ChariotTagUtils {
    const DELIMITER = '/';
    public static function lookupVariable($contextValues, $currentValues, $expr){
        $pos = strpos($expr, self::DELIMITER);
        if(false === $pos){
            return null;
        }

        $variable = null;
        if(0 === $pos){
            // abs path
            $variable = $contextValues;
            $expr = substr($expr, 1);
        } else {
            // relative path
            $variable = $currentValues;
        }

        while(false !== ($pos = strpos($expr, self::DELIMITER))){
            $target = substr($expr, 0, $pos);
            $variable = $variable[$target];

            $expr = substr($expr, $pos + 1);
            if(false === strpos($expr, self::DELIMITER)){
                $variable = $variable[$expr];
                break;
            }
        }
        return $variable;
    }
}

$start = microtime(true); 

$tpl = file_get_contents('foreach.tpl');
$tpl = file_get_contents('foreach_index.tpl');
$tpl = file_get_contents('foreach_dummy.tpl');
$tpl = file_get_contents('exists.tpl');
$tpl = mb_convert_encoding($tpl, 'HTML-ENTITIES', 'auto');

$document = new DOMDocument;
$document->validateOnParse = false;
$document->preserveWhiteSpace = false;
$document->loadXML($tpl);

$repository = new ChariotTagRepository();
$repository->set('exists', new ChariotExistsTagHandler);
$repository->set('foreach', new ChariotForeachTagHandler);
$repository->set('symfony', new ChariotSymfonyTagHandler);
$repository->set('context', new ChariotContextTagHandler);

$context = new ChariotContext;
$context->setGlobalAttibute('document', $document);
$context->setAttribute('Hoge', array(
    array('name' => 'hello', 'entry' => 'world'),
    array('name' => 'hello2', 'entry' => 'world2')
));
$context->setAttribute('Foo', array(
    'aaa' => array('name' => 'foo_name_1', 'value' => 'foo_value_1'),
    'bbb' => array('name' => 'foo_name_2', 'value' => 'foo_value_2'),
    'ccc' => array('name' => 'foo_name_3', 'value' => 'foo_value_3'),
    '1' => array('name' => 'foo_name_4', 'value' => 'foo_value_4'),
    '2' => array('name' => 'foo_name_5', 'value' => 'foo_value_5'),
    '3' => array('name' => 'foo_name_6', 'value' => 'foo_value_6')
));
$context->setAttribute('Bar', array(
    'value' => 'bar_value'
));
$context->setAttribute('Baz', array(1, 2, 3, 4, 5, 6, 7));

$root = new ChariotRootTagHandler;
$root->start($context, $repository);
var_dump($document->saveHTML());

echo 'elapsed time: ', (microtime(true) - $start), ' ms', PHP_EOL;
