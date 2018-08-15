<?php
include_once(dirname(dirname(__FILE__)).'/classes/define.php');

if (!empty($_POST['data'])) {
	$data = $_POST['data'];
	if (count($data) > 0) {
		$script = 'cd '.DOCUMENT_ROOT."\n";
		foreach ($data as $key => $value) {
			$script = $script."git add -A {$value}\n";
		}
		$script = $script."git commit -m 'update'\n";
		$script = $script."git push origin master\n";
		shell_exec($script);
		echo 1;
		exit();
	}
}
echo 0;
exit();
