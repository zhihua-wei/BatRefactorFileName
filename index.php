<?php
/**
 * Created by PhpStorm.
 * User: ZHIHUA·WEI
 * Date: 2017/12/23
 * Time: 10:30
 */

require_once 'PHPExcel.php';

$path = $_SERVER['DOCUMENT_ROOT'];

$filePath = $path . '\test\test.xlsx';

$img_path = $path . "/test/img/";

//echo $file_path;
$PHPExcel = new PHPExcel();
$PHPReader = new PHPExcel_Reader_Excel2007();
if (!$PHPReader->canRead($filePath)) {
    $PHPReader = new PHPExcel_Reader_Excel5();
    if (!$PHPReader->canRead($filePath)) {
        echo 'no Excel';
        return;
    }
}

$PHPExcel = $PHPReader->load($filePath);
/**读取excel文件中的第一个工作表*/
$currentSheet = $PHPExcel->getSheet(0);
/**取得最大的列号*/
$allColumn = $currentSheet->getHighestColumn();

/**取得一共有多少行*/
$allRow = $currentSheet->getHighestRow();

/**从第二行开始输出，因为excel表中第一行为列名*/
$name_array = array();
for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
    $file_name = $currentSheet->getCell('A' . $currentRow)->getValue();
    $new_name = $currentSheet->getCell('B' . $currentRow)->getValue();
    $name_array[] = array(
        'file_name' => $file_name,
        'new_name' => $new_name,
    );
}

foreach ($name_array as $k => $v) {
    if (empty($v['new_name']) || empty($v['file_name'])) {
        echo "new file name is empty!";
        echo "<br />";
        continue;
    } else {
        ReFileNameToNewName($img_path, $v['file_name'], $v['new_name']);
    }
}


function ReFileNameToNewName($dirName, $fileName, $newfileName)
{
    //打开此目录获取句柄资源
    if ($handle = @opendir("$dirName")) {
        while (false !== ($item = readdir($handle))) {
            //获取目录下所有文件、文件夹
            if ($item != "." && $item != "..") {
                //如果是文件则递归查询
                if (is_dir("$dirName/$item")) {
                    ReFileNameToNewName("$dirName/$item", $fileName, $newfileName);
                } else {
                    if (strstr($item, $fileName)) {
                        $ret = rename($dirName . '/' . $item, $dirName . '/' . $newfileName);
                        echo $dirName . '/' . $item . " file rename: " . $newfileName . "<br />";
                    }
                }
            }
        }
        closedir($handle);
        if (strstr($dirName, $fileName)) {
            $loop = explode($fileName, $dirName);
            $countArr = count($loop) - 1;
            if (empty($loop[$countArr])) {
                echo " <span style='color:#297C79;'><b> $dirName </b></span><br />\n";
            }
        }
    } else {
        die("path is error！");
    }
}
