<?php

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . '/../src');
require 'Chariot.php';
require 'ChariotContext.php';
require 'ChariotTagRepository.php';
require 'ChariotTagUtils.php';
require 'handler/ChariotTagHandler.php';
require 'handler/ChariotRootTagHandler.php';
require 'handler/ChariotForeachTagHandler.php';
require 'handler/ChariotExistsTagHandler.php';
require 'handler/ChariotContextTagHandler.php';

