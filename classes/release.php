<?php
include_once(dirname(dirname(__FILE__)).'/classes/define.php');

$user     = '{ユーザー}:{ハッシュ}';
$base_url = '{URL}';
$token    = '{トークン}';

//$script = 'curl -X POST -u hoge:abcdefg https://hoge.jp/build?token=abcd';
$script = 'curl -X POST -u ' . $user . ' ' . $base_url . '?token=' . $token;

$result = shell_exec($script);
if (is_null($result)) {
	echo 1;
	exit();
}
echo 0;
exit();