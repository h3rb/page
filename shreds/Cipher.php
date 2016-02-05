<?php
/*
 * Encyphering for URL-safe secure strings.
 * Low-complexity encryption method, permits huge value sizes.  Not safe for sensitive
 * transactions without being combined with SSL/AES or another, stronger method.
 */
class Vigenere {
 /*
  *  Arguments:
  *   $in      input, string
  *   $key     enciphering key, string
  *   $mode    false = decipher, else = encipher
  *   $loVal   0 (default), minimum byte value
  *   $hiVal   255 (default), maximum byte value
  *  Returns: a string, deciphered or enciphered
  */
 public function Execute($in, $key, $mode = false, $loVal = 8, $hiVal = 132) {
  $out = '';
  $inLen = strlen($in);
  $keyLen = strlen($key);
  $in = str_split($in);
  $key = str_split($key);
  $span = ($hiVal - $loVal) + 1;
  for ($pos = 0; $pos < $inLen; $pos += 1) {
   $inChar = $in[$pos];
   $keyChar = $key[($pos % $keyLen)];
   $inVal = min(max($loVal, ord($inChar)), $hiVal) -$loVal;
   $keyVal = min(max($loVal, ord($keyChar)), $hiVal) -$loVal;
   if ($mode === false) $outVal = (($inVal + $keyVal) % $span) + $loVal;
   else $outVal = (($span + $inVal - $keyVal) % $span) + $loVal;
   $out .= chr($outVal);
  }
  return $out;
 }
 public function slash_to_underscore($a) { return str_replace('/', '_', $a); }
 public function underscore_to_slash($a) { return str_replace('_', '/', $a); }
 public function plus_to_minus($a) { return str_replace('+', '-', $a); }
 public function minus_to_plus($a) { return str_replace('-', '+', $a); }
};

class Cipher {
 private $key, $longkey, $iv, $vigenere;
 function __construct($key=salt,$longkey=pepper) {
  $this->key=$key;
  $this->longkey=$longkey;
  $this->iv=mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256,MCRYPT_MODE_ECB),MCRYPT_RAND);
  $this->vigenere=new Vigenere;
 }
 // Good for short strings, like passwords.
 function mencrypt($v) {
  return rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$this->key,$v,MCRYPT_MODE_ECB, $this->iv)), "\0");
 }
 function mdecrypt($v) {
  return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$this->key,base64_decode($v),MCRYPT_MODE_ECB, $this->iv), "\0");
 }
 // Good for long strings, output is URL-safe
 public function vigdecypher($v) {
  $plus = $this->vigenere->minus_to_plus($v);
  $slash = base64_decode($this->vigenere->underscore_to_slash($plus));
  return $this->vigenere->Execute($slash, $this->longkey, false);
 }
 public function vigencypher($v) {
  $vigenere = $this->vigenere->Execute($v, $this->longkey, true);
  $underscore = $this->vigenere->slash_to_underscore(base64_encode($vigenere));
  return $this->vigenere->plus_to_minus($underscore);
 }
};
