<?php

namespace AdminBundle\Utils;

/**
 * Description of HTMLView
 *
 * @author Luis
 */
class HTMLDataView {
    public static function booleanType($boolean) {
        if($boolean) {
            return '<div class="text-center"><span class="fa fa-check"></span></div>';
        } else {
            return '';
        }
    }
    
    public static function extraType($boolean) {
        if($boolean) {
            return '<div class="text-center">Extra</div>';
        } else {
            return '<div class="text-center">Básico</div>';
        }
    }
    
    public static function numberType($value) {
        if($value != 0) {
            return '<div class="text-center">'.$value.'</span></div>';
        } else {
            return '';
        }
    }
    
    public static function percentType($value) {
        if($value != 0) {
            $p = number_format($value*100,2);
            return '<div class="text-center"><div class="progress"><div style="width: '.$p.'%;" class="progress-bar progress-bar-info">'.$p.'%</div></div></div>';
        } else {
            return '';
        }
    }
    
    public static function moneyType($value, $currency) {
        return '<div class="text-center">'.$value.' '.$currency.'</span></div>';
    }
    
    public static function transactionResultType($result) {
        $class = 'default';
        $text = 'Pendiente';
        switch($result) {
            case 'REJECT':
                $class = 'danger';
                $text = 'Rechazado';
                break;
            case 'SUCCESS':
                $class = 'success';
                $text = 'Aceptado';
                break;
            case 'FAIL':
                $class = 'warning';
                $text = 'Falló';
                break;
        }
        return '<div class="text-center"><span class="label label-sm label-'.$class.'">'.$text.'</span></div>';
    }
}
