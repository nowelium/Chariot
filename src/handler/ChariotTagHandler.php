<?php

/**
 * @author nowelium
 */
interface ChariotTagHandler {
    const URN = 'urn://Chariot';
    public function start(ChariotContext $context, ChariotTagRepository $repository);
}

