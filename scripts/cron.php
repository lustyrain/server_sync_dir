#/usr/bin/php
<?php
// 現在の相対パスを取得
$setting = json_decode(file_get_contents(dirname(__FILE__)."/../config/setting.json"), 1);
$pathTarget = $setting["output"];
$pathGetTarget = $setting["target"]["dir"];
$expire = $setting["expire"];

// ディレクトリ一覧の取得
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
				}else{
					$pathExplodeDir = explode("/".$file, $pathGetDir)[0];
					if($file === "get_sync") {
						unlink($pathGetDir);
						getFile($pathExplodeDir, $excludes, $pathTarget, $pathGetTarget);
					}else{
						$filemtime = date("Y/m/d H:i:s", filemtime($pathGetDir));
						if(strtotime($expire, $filemtime) < strtotime("now")) {
							unlink($pathGetDir);
						}
					}
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

function getFile($path, $excludes, $pathTarget, $pathGetTarget) {
	$filePathGet = explode($pathTarget, $path)[1];
	$filePathGet = $pathGetTarget . $filePathGet;
	$dirs = scandir($filePathGet);
	foreach ($dirs as $dir) {

		// 特定のディレクトリの場合は表示させない
		if (in_array($dir, $excludes)) {
			continue;
		}

		$pathDir = $filePathGet. "/". $dir;
		if ((is_dir($pathDir) === false)) {
			$dataFile = file_get_contents($pathDir);
			$pathOutput = explode($pathGetTarget, $pathDir)[1];
			$pathOutput = $pathTarget. $pathOutput;
			file_put_contents($pathOutput, $dataFile);
		}
	}
}
