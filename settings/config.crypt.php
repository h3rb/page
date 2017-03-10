<?php
/*
 * Updated for 5.6
 * Change these to whatever you want.
 */
function ourcrypt($pwd) {
 return password_hash($pwd,PASSWORD_DEFAULT);
}

function matchcrypt($input,$pass) {
 return password_verify($input,$pass);
}
