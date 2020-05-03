/** Copyright (C) 2000 Artificial Intelligence NV **/
/*
 *		This is the API call the client uses to perform a particular action.
 *		The action itself is specified via a string with the format
 *		"action:data".  Each action corresponds to a function call.
 */
function perform_action(action)
{
	command=action.split(":",2);
	switch(command[0].toLowerCase())
	{
		case "show": action_show(); break;
		case "hide": action_hide(); break;
		case "exit": action_exit(); break;
		case "back": action_back(); break;
		case "forward": action_forward(); break;
		case "home": action_home(); break;
		case "print": action_print(); break;
		case "about": action_about(); break;
		case "cache": action_cache(); break;
		case "plugins": action_plugins(); break;
		case "source": action_source(); break;
		case "stop": action_stop(); break;
		case "search": action_search(command[1]); break;
		case "find": action_find(command[1]); break;
		case "url": action_url(command[1]); break;
		case "email": action_email(command[1]); break;
		case "alert": action_alert(command[1]); break;
		case "error": action_error(command[1]); break;
		default:
			action_error("Unknown action \""+command[0]+"\".");
			break;
	}
}
function action_show()
{
	if(interface_hidden==true) toggle_interface();
	return true;
}
function action_hide()
{
	if(interface_hidden==false) toggle_interface();
	clear_hide_timer();
	clear_history_timer();
	return true;
}
function action_exit()
{
	window.close();
}
function action_back()
{
	window.frames["BrowserFrame"].window.back();
}
function action_forward()
{
	window.frames["BrowserFrame"].window.forward();
}
function action_home()
{
	window.frames["BrowserFrame"].window.home();
}
function action_print()
{
	window.frames["BrowserFrame"].window.print();
}
function action_stop()
{
	window.frames["BrowserFrame"].window.stop();
}
function action_about()
{
	window.frames["BrowserFrame"].window.location="about:";
}
function action_cache()
{
	window.location="about:cache";
}
function action_plugins()
{
	window.location="about:plugins";
}
function action_source()
{
	window.location="view-source:";
}
function action_search(data)
{
	window.frames["BrowserFrame"].window.location="http://www.google.com/search?q="+escape(data);;
}
function action_find(data)
{
	window.find(data);
}
function action_url(data)
{
	if(data.slice(0,4).toLowerCase()!="http") data="http://"+data+"/";
	window.frames["BrowserFrame"].window.location=data;
}
function action_email(data)
{
	if((data.length>0)&&(data.search(/@/gi)==-1)) data=data+"@a-i.com";
	window.frames["BrowserFrame"].window.location="mailto:"+data;
}
function action_alert(data)
{
	alert(data);
}
function action_error(data)
{
	alert("ERROR: "+data);
}
/** Copyright (C) 2000 Artificial Intelligence NV **/
