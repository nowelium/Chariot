<?php

require dirname(__FILE__) . '/bootstrap.php';

class ChariotTest extends PHPUnit_Framework_TestCase {
    public function testRender(){
        $chariot = new Chariot;
        $chariot->setTemplateDirectory(dirname(__FILE__) . '/templates');
        $chariot->setContext(new ChariotContext);
        $result = $chariot->render('example/test1');

        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/results/example/test1.html'), $result, 'DOM構造に変化させずに出力すること');
    }
}
