<?php
$config = [
'password' => 'password',    					//码云webhook密码
	'projects' => [
	'branch' => 'master',    					//分支
	'web_path' => '/data/www/testnhb',    		//服务器项目路径
	],
	];
header("Content-type:text/html; charset=utf-8");
if(!isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	header('HTTP/1.1 304 Not Modified'); exit;
}
$data = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);
//判断密码
if($data['password'] != $config['password'])
{
	header('HTTP/1.1 304 Not Modified'); exit;
}
//判断分支
$branch = trim(strrchr($data['ref'], '/'), '/');
if($branch != $config['projects']['branch'])
{
	header('HTTP/1.1 304 Not Modified'); exit;
}
//管道
$descriptorspec = array(
		0 => array("pipe", "r"),
		1 => array("pipe", "w"),
		2 => array("pipe", "w"),
		);
$process = proc_open('git pull',$descriptorspec,$pipes,$config['projects']['web_path'],null);
if(is_resource($process))
{
	$output .= stream_get_contents($pipes[1]);
	fclose($pipes[1]);
	$output .= stream_get_contents($pipes[2]);
	fclose($pipes[2]);
}
$return_value = proc_close($process);
file_put_contents('logs/webhooks.log',$output,FILE_APPEND);

?>
