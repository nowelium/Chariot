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
    public function testRender_if_not_exists_template(){
        $chariot = new Chariot;
        $chariot->setTemplateDirectory(dirname(__FILE__) . '/templates');
        $chariot->setContext(new ChariotContext);
        try {
            $chariot->render('example/hoge_foo_bar');
            $this->fail('存在しないパスは例外が出力されること');
        } catch(RuntimeException $e){
            echo $e->getMessage(), PHP_EOL;
        }
    }
    public function testRender_php_evaluated(){
        $chariot = new Chariot;
        $chariot->setTemplateDirectory(dirname(__FILE__) . '/templates');
        $chariot->setContext(new ChariotContext);
        $result = $chariot->render('example/test2');

        $this->assertEquals(file_get_contents(dirname(__FILE__) . '/results/example/test2.html'), $result, '<?php タグの評価を行うこと');
    }
}
