<?php

/**
 * @author nowelium
 */
class Chariot {

    protected $context;
    protected $templateDirectory;
    protected $templatePrefix = '.tpl';

    public function setTemplateDirectory($directory){
        $this->templateDirectory = $directory;
    }
    public function getTemplateDirectory(){
        return $this->templateDirectory;
    }
    public function setContext(ChariotContext $context){
        $this->context = $context;
    }
    public function getContext(){
        return $this->context;
    }

    public function render($templateName){
        $path = $this->templateDirectory . '/' . $templateName . $this->templatePrefix;
        if(!file_exists($path)){
            throw new RuntimeException('file not found: ' . $path);
        }
        $tpl = $this->getContents($path);
        $tpl = mb_convert_encoding($tpl, 'HTML-ENTITIES', 'auto');

        $document = new DOMDocument;
        // dtd の検証を行う
        $document->validateOnParse = false;
        // 余分なwhitespaceを取り除く
        $document->preserveWhiteSpace = false;
        // 整形した出力
        //$document->formatOutput = true;
        $document->loadXML($tpl);

        $repository = $this->getTagRepository();
        $context = $this->context;
        $context->setGlobalAttibute('document', $document);

        $handler = new ChariotRootTagHandler;
        $handler->start($context, $repository);

        return $document->saveHTML();
    }

    protected function getContents($path){
        ob_start();
        include $path;
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;

    }

    protected function getTagRepository(){
        $repository = new ChariotTagRepository();
        $repository->set('exists', new ChariotExistsTagHandler);
        $repository->set('foreach', new ChariotForeachTagHandler);
        $repository->set('context', new ChariotContextTagHandler);
        return $repository;
    }
}
