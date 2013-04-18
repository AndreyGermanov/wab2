objArgs = WScript.Arguments;
str = "";
for (i=0;i<objArgs.length;i++) {
        if (i>0)
		str = str + objArgs(i)+" ";
	else
		url = objArgs(i);
}
var WshShell = new ActiveXObject("Wscript.Shell");
WScript.Echo("cmd /C \"start /B "+url+"/?path=Z"+str+"\"");
oExec=WshShell.exec("cmd /C \"start /B "+url+"/?path="+encodeURIComponent(str)+"\"");
WScript.Echo(oExec.Status);
WScript.sleep(2000);
