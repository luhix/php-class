<?php

/*----------------------------------------------------------------------------------

 *

 *----------------------------------------------------------------------------------

 */

class image_up{

 //定义基本参数

 private $uptype=array('image/jpg','image/jpeg','image/png','image/pjpeg','image/gif','image/bmp','image/x-png');  //上传文件类型

 private $max_file_size=102400;    //上传大小限制(单位:KB)

 private $destination_folder="up/"; //上传文件路径

 private $watermark=1;              //是否附加水印

 private $watertype=1;              //水印类型(1为文字,2为图片)

 private $waterposition=1;          //水印位置(1为左下角,2为右下角,3为左上角,4为右上角,5为居中);

 private $waterstring=null;         //水印字符串

 private $waterimg=null;            //水印图片

 private $imgpreview=1;             //是否生成预览图(1为生成,其他为不生成);

 private $imgpreviewsize=1;         //预览图比例，0为按固定宽和高显示，其他为比例显示

 private $imgwidth=200;             //预览图固定宽度

 private $imgheight=200;            //预览图固定高度

 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

 private $imgthu=1;                     //是否生成且保存略缩图,1为生成，0为不生成

 private $imgthu_folder=null;           //略缩图保存路径,默认与文件路径一致

 private $imgthu_fixed=0;               //略缩图是否使用固定宽高，1为使用，0为灵活变动

 private $imgthu_width=200;             //略缩图宽度

 private $imgthu_height=200;            //略缩图高度



 private $imgthu_name=null;             //略缩图名称

 //******************************************************************************************************************

 private $inputname="upfile";       //文件上传框名称

 //******************************************************************************************************************

 private $img_preview_display;      //图片预览图显示

 //******************************************************************************************************************

 //文件上传相关信息，1为文件不存在，2为类型不符合，3为超出大小限制，4为上传失败，0为上传成功

 private $file_up_info=null;

 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

 //可在外部获取上传文件基本信息

 private $file_name;         //客服端文件的原名称

 private $file_type;         //文件的MIME类型

 private $file_size;         //已上传文件的大小，单位/字节

 private $file_tmp_name;     //储存的临时文件名

 private $file_error;        //该文件上传相关错误代码
private $img_size;          //取得图片的长宽

 private $file_basename;     //获取带扩展名的全名

 private $file_extension;    //获取文件扩展名

 private $filename;          //文件名（不带扩展名)

 private $destination;       //问价路径加名称

 //******************************************************************************************************************

 public function __set($propety_name,$value){

  $this->$propety_name=$value;

 }

 public function __get($property_name){

  if(isset($this->$property_name))

  return($this->$property_name);

  else return(NULL);

 }

 //******************************************************************************************************************

 //定义文件上传功能

 public function up(){

  //判断文件是否存在

  if(!is_uploaded_file($_FILES[$this->inputname]["tmp_name"])){

   $this->file_up_info=1;

   return;

  }

  //获取并赋值相应基本参数

  $upfile=$_FILES[$this->inputname];

  $this->file_name=$upfile["name"];

  $this->file_type=$upfile["type"];

  $this->file_size=$upfile["size"];

  $this->file_tmp_name=$upfile["tmp_name"];

  $this->file_error=$upfile["error"];

  //检查文件类型是否符合

  if(!in_array($this->file_type,$this->uptype)){

   $this->file_up_info=2;

   return;

  }

  //检查文件大小是否超出限制

  if($this->file_size>$this->max_file_size){

   $this->file_up_info=3;

   return;

  }

  //判断目录是否存在

  if(!file_exists($this->destination_folder))

  mkdir($this->destination_folder);

  //进一步取得图片的信息并赋值

  $this->img_size=getimagesize($this->file_tmp_name);

  $pathinfo=pathinfo($this->file_name);

  $this->file_extension=$pathinfo["extension"];    //获取文件扩展名

  $this->file_basename=$pathinfo["basename"];      //获取带扩展名的全名

  $this->filename=$pathinfo["filename"];           //文件名（不带扩展名)

  $filename2=$pathinfo['filename'];

  $this->destination = $this->destination_folder.$this->filename.".".$this->file_extension;

  //判断文件名是否存在，如果存在则重命名

  $n=1;

  while (file_exists($this->destination)){

   while (file_exists($this->destination)){

    $n++;

    $this->filename=$this->filename."(".$n.")";

    $this->destination = $this->destination_folder.$this->filename.".".$this->file_extension;

   }

   $this->filename=$filename2."(".$n.")";

   $this->destination = $this->destination_folder.$this->filename.".".$this->file_extension;

  }

  //移动上传的文件

  if(move_uploaded_file($this->file_tmp_name,$this->destination))

  $this->file_up_info=0;

  else $this->file_up_info=4;



  //添加水印

  if($this->watermark==1){

   $this->imgthu();

  }

  //生成略缩图

  if($this->imgthu==1){

   $this->add_watermark();

  }

  //生成预览图

  if($this->imgpreviewsize == 0){

   if($this->img_size["0"]<$this->imgwidth) $this->imgwidth=$this->img_size["0"];

   if($this->img_size["1"]<$this->imgheight) $this->imgheight=$this->img_size["1"];

  }else{

   $this->imgwidth=$this->img_size["0"]*$this->imgpreviewsize;

   $this->imgheight=$this->img_size["1"]*$this->imgpreviewsize;

  }

  $this->img_preview_display="<img src='$this->destination' width='$this->imgwidth' height='$this->imgheight'

                                    alt='图片预览：r文件名'：$this->file_tmp_name />";

 }

//====================================================================================================================

//====================================================================================================================

 //生成略缩图功能

 function imgthu(){

  if($this->imgthu_folder==null)

    $this->imgthu_folder=$this->destination_folder;



  //$this->imgthu_name=$this->filename."_t.".$this->file_extension;

  $imgthu_name_b=$this->filename."_t";

  $imgthu_name_b2=$this->filename."_t";

  $destination_b=$this->imgthu_folder.$imgthu_name_b.".".$this->file_extension;

     //判断文件名是否存在，如果存在则重命名

  $n=1;

  while (file_exists($destination_b)){

   while (file_exists($destination_b)){

    $n++;

    $imgthu_name_b=$imgthu_name_b."(".$n.")";

    $destination_b = $this->imgthu_folder.$imgthu_name_b.".".$this->file_extension;

   }

   $imgthu_name_b=$imgthu_name_b2."(".$n.")";

   $destination_b = $this->imgthu_folder.$imgthu_name_b.".".$this->file_extension;

  }





  $imginfo=getimagesize($this->destination);

  switch($imginfo[2])

  {

   case 1:

    $in=@imagecreatefromgif($this->destination);

    break;

   case 2:

    $in=@imagecreatefromjpeg($this->destination);

    break;

   case 3:

    $in=@imagecreatefrompng($this->destination);

    break;

   case 6:

    $in =@imagecreatefrombmp($this->destination);

    break;

   default:

    break;

  }

  //计算略缩图长宽

  if($this->imgthu_fixed==0){

   if($this->imgthu_height>($imginfo[1]/$imginfo[0])*$this->imgthu_width)

    $this->imgthu_width = ($imginfo[0]/$imginfo[1])*$this->imgthu_height;

   else

    $this->imgthu_height=($imginfo[1]/$imginfo[0])*$this->imgthu_width;

  }

  $new = imageCreateTrueColor($this->imgthu_width,$this->imgthu_height);

  ImageCopyResized($new,$in,0,0,0,0,$this->imgthu_width,$this->imgthu_height,$imginfo[0],$imginfo[1]);

  switch ($imginfo[2])

  {

   case 1:

    imagejpeg($new,$destination_b);

    break;

   case 2:

    imagejpeg($new,$destination_b);

    break;

   case 3:

    imagepng($new,$destination_b);

    break;

   case 6:

    imagewbmp($new,$destination_b);

    break;

  }

 }

//====================================================================================================================

//====================================================================================================================

 //添加水印功能

 function add_watermark(){

  //1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，

  //8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM。

  $imginfo=getimagesize($this->destination);

  $im=imagecreatetruecolor($this->img_size[0],$this->img_size[1]);       //创建真彩色

  $white=imagecolorallocate($im,255,255,255);                            //设置颜色

  $black=imagecolorallocate($im,0,0,0);

  $red=imagecolorallocate($im,255,0,0);

  //在 image 图像的坐标 x，y（图像左上角为 0, 0）处用 color 颜色执行区域填充（即与 x, y 点颜色相同且相邻的点都会被填充）。

  imagefill($im,0,0,$white);
 switch($imginfo[2])

  {

   case 1:

    $simage =imagecreatefromgif($this->destination);      // 创建一个新的形象，从文件或 URL

    break;

   case 2:

    $simage =imagecreatefromjpeg($this->destination);

    break;

   case 3:

    $simage =imagecreatefrompng($this->destination);

    break;

   case 6:

    $simage =imagecreatefromwbmp($this->destination);

    break;

   default:

    echo ("不支持的文件类型");

    break;

  }

  if(!empty($simage))

  {

   //位置设置

   if($this->watertype==1){

    $str_len=strlen($this->waterstring);

       $str_width=$str_len*10;

       $str_height=20;

   }elseif($this->watertype==1 && file_exists($this->waterimg)){

    $iinfo=getimagesize($this->waterimg);

    $str_width = $iinfo[0];

    $str_height = $iinfo[1];

   }



   switch ($this->waterposition){

    case 1:

     $p_x=5;

     $p_y=$this->img_size[1]-$str_height;

     break;

    case 2:

     $p_x=$this->img_size[0]-$str_width;

     $p_y=$this->img_size[1]-$str_height;

     break;

    case 3:

     $p_x=5;

     $p_y=0;

     break;

    case 4:

     $p_x=$this->img_size[0]-$str_width;

     $p_y=5;

     break;

    case 5:

     $p_x=($this->img_size[0]-$str_width)/2;

     $p_y=($this->img_size[1]-$str_height)/2;

     break;

   }

   imagecopy($im,$simage,0,0,0,0,$this->img_size[0],$this->img_size[1]);   //拷贝图像的一部分

   //imagefilledrectangle($im,1,$this->img_size[1]-15,130,$this->img_size[1],$white);  //将图片的封闭长方形区域着色
  switch($this->watertype)

   {

    case 1:   //加水印字符串

     imagestring($im,10,$p_x,$p_y,$this->waterstring,$red);

     break;

    case 2:   //加水印图片

     $simage1 =imagecreatefromgif($this->waterimg);

     imagecopy($im,$simage1,0,0,0,0,85,15);

     imagedestroy($simage1);

     break;

   }
      switch ($imginfo[2])

   {

    case 1:

     //imagegif($nimage, $destination);

     imagejpeg($im, $this->destination);

     break;

    case 2:

     imagejpeg($im, $this->destination);

     break;

    case 3:

     imagepng($im, $this->destination);

     break;

    case 6:

     imagewbmp($im, $this->destination);

     break;

   }

   //覆盖原上传文件

   imagedestroy($im);

   imagedestroy($simage);

  }

 }

}