<?php

require dirname(__FILE__) . '/bootstrap.php';

class ChariotExistsTest extends PHPUnit_Framework_TestCase {
    public function testExists(){
        $context = new ChariotContext;
        $context->setAttribute('Hoge', 'hello world');

        $chariot = new Chariot;
        $chariot->setTemplateDirectory(dirname(__FILE__) . '/templates');
        $chariot->setContext($context);
        $result = $chariot->render('example/exists1');

        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/results/example/exists1.html'), $result, 'existsでは存在する値のみ出力すること');
    }

    public function testNotExists(){
        $context = new ChariotContext;
        $context->setAttribute('Hoge', 'hello world');

        $chariot = new Chariot;
        $chariot->setTemplateDirectory(dirname(__FILE__) . '/templates');
        $chariot->setContext($context);
        $result = $chariot->render('example/exists2');

        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/results/example/exists2.html'), $result, 'notExistsでは存在しない値も出力すること');
    }
}
