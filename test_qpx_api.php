<?php
/**
 * Created by PhpStorm.
 * User: mgrinchenko
 * Date: 3/28/2017
 * Time: 6:08 PM
 */

include "includes/apis/QPX.php";
include "includes/Requests.php";
Requests::register_autoloader();

$qpx = new QPX();

$qpx->get_plan();