<?php
/**
 * @Author: Luhix
 * @Date:   2017-08-31 13:53:57
 * @Last Modified by:   Luhix
 * @Last Modified time: 2017-11-16 15:18:16
 */
include 'download.class.php';

$dl = new download();
$f = '20.jpg';
if(!$dl ->downloadfile($f)) {
    echo $dl->geterrormsg();
}