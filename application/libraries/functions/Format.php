<?php

class Format {

    public static function Currency($value, $currencySymbol = 'RM') {
        return $currencySymbol . ' ' . number_format($value, 2, '.', ',');
    }
    
    public static function Weight($value, $weightSymbol = 'g'){
        return  number_format($value, 2, '.', ','). ' ' . $weightSymbol;
    }

}
