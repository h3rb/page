<?php

 function ourcrypt($pwd) {
  return md5($pwd.pepper);
 }
