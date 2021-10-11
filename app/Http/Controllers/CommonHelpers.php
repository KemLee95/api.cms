<?php
namespace App\Http\Controllers;

class CommonHelpers {
  
  public static function generateRandomString($char, $len = 10) {
    
    $charLength = strlen($char);
    $ramdomString = '';
    for($i=0; $i< $len; $i++) {
      $ramdomString .= $char[rand(0, $charLength -1)];
    }
    return $ramdomString;
  }

  public static function getUniqueId() {
    return strval(uniqid()) . strval(time());
  }
  public static function getUniqueCode() {
    return strval(uniqid());
  }
}