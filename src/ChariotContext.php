<?php

/**
 * @author nowelium
 */
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

