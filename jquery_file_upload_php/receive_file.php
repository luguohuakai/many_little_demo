<?php
/**
 * Created by PhpStorm.
 * User: DM
 * Date: 2017/12/18
 * Time: 23:26
 */

//包含一个文件上传类中的上传类
require_once "fileupload.class.php";
$path = "./files/";

$up = new fileupload;
//设置属性(上传的位置， 大小， 类型， 名是是否要随机生成)
$up->set("path", $path); // 注意: 此路径需要777权限 755也行
$up->set("maxsize", 2000000);
$up->set("allowtype", array('txt', 'sql'));
$up->set("israndname", false);

//使用对象中的upload方法， 就可以上传文件， 方法需要传一个上传表单的名子 pic, 如果成功返回true, 失败返回false
if ($up->upload("files")) {
    // 上传文件名称
    $file_name = $up->getFileName();
    // 分析文件内容 逐行分析
    $title = $contents = '';
    $flag = 0;
    $myfile = fopen($path . $file_name[0], "r") or die("Unable to open file!");
    while (!feof($myfile)) {
        $this_line = fgets($myfile);
        if (!$title) {
            if (substr($this_line, 0, 6) == '标题') {
                $title = trim(str_replace('标题:', '', $this_line));
                continue;
            }
        }

        if (substr($this_line, 0, 8) == '[正文]') {
            $flag++;
            continue;
        }
        if (substr($this_line, 0, 8) == '[编后]') break;
        if ($flag > 0) $contents .= $this_line;
    }
    fclose($myfile);

    // 返回文件内容
    $re['title'] = $title;
    $re['contents'] = $contents;

    // 删除文件
    unlink($path . $file_name[0]);
} else {
    //获取上传失败以后的错误提示
    $re['msg'] = $up->getErrorMsg();
}

exit(json_encode($re));