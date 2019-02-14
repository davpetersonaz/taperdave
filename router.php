<?php
include_once('config.php');

$p = (isset($_GET['p']) ? $_GET['p'] : '');
logDebug('router, p='.$p);
if(empty($p)){//default
	$p = 'home';
}elseif(substr($p, -4) === '.php'){//strip .php
	$p = (substr($p, 0, -4));
}

$queryArray = explode('/', $p);
$page = (!empty($queryArray) ? $queryArray[0] : 'home');//shouldn't be necessary, but maybe $_GET['p'] is NULL
logDebug('page='.$page);

//new way to show a show
if($queryArray[0] === 'showinfo' && count($queryArray) > 2){
	$page = 'showinfo';
}else

//handle show links to the text files
if($queryArray[0] === 'files' && count($queryArray) > 1){
	file_get_contents('/'.$p);
	exit;
}else
	
//and finally handle unknown pages
if(file_exists(HTMLS_PATH.$page.'.php') !== true){
	logDebug('page doesnt exist: '.$page);
	$page = 'home';//default to home
}

//and go on to display the page requested