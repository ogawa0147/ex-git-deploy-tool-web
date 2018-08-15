<?php
include_once(dirname(dirname(__FILE__)).'/classes/define.php');

$tmp = shell_exec('cd '.DOCUMENT_ROOT."\n".'git status -uall -s');
$array = preg_split('/\n/', mb_convert_kana($tmp, 's'), -1, PREG_SPLIT_NO_EMPTY);

$data = array();
$sort = array();

foreach ($array as $key => $value) {
	$a = preg_split('/[\s]+/', mb_convert_kana($value, 's'), -1, PREG_SPLIT_NO_EMPTY);

	$status = $a[0];
	$path = $a[1];
	$update_at = filemtime(DOCUMENT_ROOT.'/'.$a[1]);

	$data[$key]['status'] = $status;
	$data[$key]['path'] = $path;
	if ($a[0] != 'D') {
		if ($status == '??') {
			$data[$key]['status'] = 'A';
		}
		$data[$key]['update_at'] = date('Y-m-d H:i:s', $update_at);
	} else {
		$data[$key]['update_at'] = date('9999-01-01 00:00:00');
	}
	$sort[$key] = $data[$key]['update_at'];
}
array_multisort($sort, SORT_DESC, $data);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
exit();
