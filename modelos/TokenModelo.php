<?php

class Token {
  public static function generate() {
    return bin2hex(random_bytes(32));
  }

  public static function check($token, $sessionToken) {
    return $token === $sessionToken;
  }
}