<?php

/**
 * @author nowelium
 */
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
