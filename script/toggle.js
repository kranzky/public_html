/*
 *		Open/Close the NavBot window if F10 is depressed (mouse click under
 *		Nav4 on Un*x.
 */

var navwin = null;

hotkey_init(121,togglebot);

function togglebot(e)
{
	if((navwin==null)||(navwin.closed))
	{
		if(is_nav5up) netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserWrite");
		navwin=window.open("NavBot.html","_navbot","screenX=300,screenY=300,alwaysRaised,dependent,innerHeight=100,innerWidth=400,titlebar=no");
		navwin.innerHeight=100;
		if(is_nav5up) netscape.security.PrivilegeManager.disablePrivilege("UniversalBrowserWrite");
		navwin.focus();
	}
	else
	{
		navwin.close();
	}
	return false;
}
