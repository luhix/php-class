<?php
/**
 * PHP文件上传处理页面
 */

include 'waterInfo.php';

// 定义提示函数

function alert($msg)
{

    return '<script type="text/javascript">alert("' . $msg . '");window.history.back(-1);</script>';
}

// 定义允许的文件类型

$allowType = array('image/jpeg', 'image/gif', 'image/jpg', 'image/png');

// 定义路径，可以是绝对路径，或者相对路径都可以

$filePath = './uploadFileDir/' . date("Y-m-d", time()) . '/';

// 接收表单信息 其中里边写的 file 值是 静态页form表单里的name值

$file = $_FILES['file'];


// 第一步，判断上传的文件是否有错误
if ($file['error'] !== 0) {

    exit(alert('文件上传错误'));
}

// 第二步，判断文件大小，这里的102400是字节，换算为kb就是100kb
if ($file['size'] > 102400) {
    exit(alert('文件过大'));
}

// 第三步，判断文件类型
if (!in_array($file['type'], $allowType)) {
    exit(alert('文件类型错误'));
}

// 第四步，判断路径是否存在，如果不存在则创建
if (!file_exists($filePath) && !mkdir($filePath, 0777, true)) {
    exit(alert('创建目录错误'));
}
// 第五步，定义上传后的名字及路径
$filename = time() . '_' . $file['name'];
// 第六步，复制文件
if (!move_uploaded_file($file['tmp_name'], $filePath . $filename)) {
    exit(alert('上传文件出错，请稍候重试'));
} else {
    $wt = new Water();

    $water = 'water.png'; //水印图片
    echo $filePath . $filename;
    $s = $wt->waterInfo($filePath . $filename, $water, 5); //其它默认就可以了。

    echo $s;

    // echo alert('恭喜，上传文件['.$filename.']成功！');
}

// 第七步，删除临时文件
// unlink($file['tmp_name']);
// 提示上传成功