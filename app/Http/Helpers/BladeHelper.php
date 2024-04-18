<?php 
namespace App\Http\Helpers\BladeHelper;

class BladeHelper{

    function getDir(){
        dd("here");
        return session()->get('lang')=='en'?'ltr':'rtl';
    }
}