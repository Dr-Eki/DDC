<?php

if($environment == 'local')
{
  error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
}
else
{
  error_reporting(0);
}

if($_GET['delete_cookies'] == 1)
{
	setcookie('ddc_experimental',NULL,time()-1000);
	setcookie('ddc_style',NULL,time()-1000);		

	echo '<pre>Cookies deleted. <a href="./">Go back.</a></pre>';

}
elseif($_GET['style'] == 'light' or $_GET['style'] == 'dark')
{
	setcookie('ddc_style', $_GET['style'],  time()+60*60*24*30, '/ddc/');
	header('Location: /ddc/');
}
elseif($_GET['experimental'] == 'enable' or $_GET['experimental'] == 'disable')
{
	setcookie('ddc_experimental', $_GET['experimental'],  time()+60*60*24*30, '/ddc/');
	header('Location: /ddc/');
}
elseif($_GET['reset'] == 1)
{
	# Do nothing?
	unset($_SESSION);
	header('Location: /ddc/');
}
else
{
	header('Location: /ddc/');
}