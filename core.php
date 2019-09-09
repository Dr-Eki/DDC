<?php

if($_SERVER['SERVER_ADDR'] == '::1')
{
	$environment = 'local';
}

DEFINE('ROUNDING_PRECISION', 4);