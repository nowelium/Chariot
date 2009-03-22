<?php

/**
 * @author nowelium
 */
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
