/*
 *		A script may request bind one (and only one) keypress with an action
 *		by making a call to hotkey_init.  Navigator 4 on Un*x ignores
 *		keypresses, so a mouse click on the browser window replaces the key.
 */

var hotkey_key = 0;
var hotkey_action = null;

function hotkey_init(key,action)
{
	if((is_nav4)&&(is_unix))
	{
		document.onmousedown = action;
		document.captureEvents(Event.MOUSEDOWN);
	}
	else
	{
		hotkey_key=key;
		hotkey_action=action;
		document.onkeydown = hotkey;
		if(is_nav4) document.captureEvents(Event.KEYDOWN);
	}
}

function hotkey(e)
{
	var key;
	if(is_nav) key=e.which;
	if(is_ie) key=event.keyCode;
	if(key==hotkey_key) return hotkey_action(e);
	return true;
}
