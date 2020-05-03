onload = init;

perform_action = new Array();

var secure_action = null;

function hide(e)
{
	window.close();
	return false;
}

function init()
{
	redraw();
	hotkey_init(121,hide);
	document.title="Natural Language Web Navigation System";
	if(is_nav5up)
	{
		document.forms[0].onsubmit=question;
	}
	else if(is_nav4)
	{
		document.Input.document.Form.onsubmit=question;
	}
	else if(is_ie)
	{
		document.Form.onsubmit=question;
	}
	perform_action["close"] = action_close;
	perform_action["bye"] = action_close;
	perform_action["quit"] = action_close;
	perform_action["exit"] = action_close;
	perform_action["back"] = action_back;
	perform_action["forward"] = action_forward;
	perform_action["home"] = action_home;
	perform_action["navbot"] = action_navbot;
	perform_action["print"] = action_print;
	perform_action["stop"] = action_stop;
	perform_action["interrupt"] = action_stop;
	perform_action["halt"] = action_stop;
	perform_action["search"] = action_search;
	perform_action["find"] = action_search;
	perform_action["go"] = action_go;
	perform_action["url"] = action_go;
	perform_action["surf"] = action_go;
	perform_action["email"] = action_email;
	perform_action["mail"] = action_email;
	/*
	 *		Create a new function rather than normal way because nav4 complains
	 *		about try keyword, even if code is never executed.
	 */
	if(is_nav5up)
	{
		secure_action = new Function("comman","parameter","try { perform_action[command](parameter); } catch(all) { denied(); }");	
	}
}

function denied()
{
	answer("You haven't granted me permission to do that.  Sorry.");
}

function redraw()
{
}

function question()
{
	if(is_nav5up)
	{
		user=document.forms[0].elements[0].value;
		document.forms[0].elements[0].value="";
	}
	else if(is_nav4)
	{
		user=document.Input.document.Form.Question.value;
		document.Input.document.Form.Question.value="";
	}
	else if(is_ie)
	{
		user=document.Form.Question.value;
		document.Form.Question.value="";
	}
	extract=user.match(/^\s*(\S*)\s*(.*)\s*$/i);
	command=extract[1].toLowerCase();
	parameter=extract[2];
	if(perform_action[command])
	{
		if(is_nav4) perform_action[command](parameter);
		else secure_action(command,parameter);
	}
	else
	{
		answer("I don't understand what you mean.");
	}
	return false;
}

function answer(text)
{
	if(is_nav5up)
	{
		paragraph=document.getElementById("Reply").firstChild;
		paragraph.deleteData(0,paragraph.length);
		paragraph.appendData(text);
	}
	else if(is_nav4)
	{
		document.Output.document.open();
		document.Output.document.write("<P>"+text+"</P>");
		document.Output.document.close();
		alert(text);
	}
	else if(is_ie)
	{
		document.all["Output"].innerHTML="<P>"+text+"</P>";
	}
}

function additional(text)
{
	paragraph=document.getElementById("Reply").firstChild;
	paragraph.appendData("  "+text);
}

function action_close(text)
{
	answer("Okay, I'm outta here!");
	window.close();
}

function action_back(text)
{
	answer("Sure, I'll take you back a page.");
	if(is_nav5up) netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserWrite");
	window.opener.window.back();
	if(is_nav5up) netscape.security.PrivilegeManager.disablePrivilege("UniversalBrowserWrite");
}

function action_forward(text)
{
	answer("No problems; I'll take you forward.");
	window.opener.window.forward();
}

function action_home(text)
{
	answer("Right.  I'm about to take you home.");
	window.opener.window.home();
}

function action_print(text)
{
	answer("I'll just send the page to the printer.");
	window.opener.window.print();
}

function action_stop(text)
{
	answer("I'm going to interrupt the transfer now.");
	window.opener.window.stop();
}

function action_search(text)
{
	answer("My friend Google will find that for you.");
	window.opener.window.location="http://www.google.com/search?q="+escape(text);
}

function action_email(text)
{
	answer("I'll open up the email composer for you.");
	if((text.length>0)&&(text.search(/@/gi)==-1)) text=text+"@a-i.com";
	window.opener.window.location="mailto:"+text;
}

function action_go(text)
{
	answer("Let me take you there immediately.");
	if(text.slice(0,4).toLowerCase()!="http") text="http://"+text+"/";
	window.opener.window.location=text;
}

function action_navbot()
{
	answer("Welcome to my home page.");
	window.opener.window.location="http://10.111.112.3/~hutch/navbot/";
}
