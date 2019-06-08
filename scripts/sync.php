<?php
// 現在の相対パスを取得
$setting = json_decode(file_get_contents(dirname(__FILE__)."/../config/setting.json"), 1);
$pathTarget = $setting["target"]["dir"];

// ディレクトリ一覧の取得
//$dirs = scandir($path);

$checkDirs = [$pathTarget];
$outputDir = [];

// 表示させないディレクトリ配列
$excludes = array(
	'.',
	'..',
	'.DS_Store',
	'.idea',
	'.git'
);

while( $checkDirs ) {
	$pathDir = $checkDirs[0];

	try {
		if (is_dir($pathDir) && $handle = opendir($pathDir)) {
			while (($file = readdir($handle)) !== false) {
				if (in_array($file, $excludes) === true) {
					continue;
				}
				$pathGetDir = rtrim($pathDir, "/") . "/" . $file;


				if (is_dir($pathGetDir) === true) {
					$checkDirs[] = $pathGetDir;
					$outputDir[] = $pathGetDir;
//					var_dump($path);
				}
			}
		}

		array_shift($checkDirs);
	}catch (Exception $e) {

	}
}
if (!file_exists($setting["output"])){
	mkdir($setting["output"]);
}
foreach ($outputDir as $item) {
	$outputPath = explode($pathTarget,$item)[1];
	$pathMakeDir = $setting["output"] . $outputPath;
	mkdir($pathMakeDir, 0777, true);
}
