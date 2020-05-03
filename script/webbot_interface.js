/** Copyright (C) 2000 Artificial Intelligence NV **/
/*
 *		This file contains functions for displaying and updating the interface.
 */
onload = init;
/*
 *		These variables control the behaviour and the appearance of the
 *		interface.
 */
var scroll_speed=11;			/* number of pixels the history display scrolls by */
var mini_timeout=10000;		/* msecs of inactivity before minimisation */
var hist_timeout=30000;		/* msecs of inactivity before history window hides */
var hide_timeout=60000;		/* msecs of inactivity before interface hides */
var poll_timeout=600000;	/* msecs of inactivity before server is polled */
var check_timeout=250;		/* msecs before checking the browser frame */
var arrow_offset=10;			/* number of pixels scroll arrows are inset by */
var history_inset=10;		/* number of pixels history window is inset by */
var interface_left=20;		/* the horizontal position of the interface */
var interface_top=0;			/* the vertical position of the interface */
var interface_width=650;	/* the width of the interface */
var interface_height=133;	/* the height of the interface at maximum size */
/*
 *		These variables reflect the initial status of the interface.
 */
var interface_minimised=false;
var interface_hidden=false;
var history_hidden=false;
/*
 *		These variables are dynamically calculated during runtime.  All
 *		variables need to be initialised to give them global scope.
 */
var input_height=0;
var key_speed=0;
var old_location=null;
/*
 *		These variables are short-cut references to layers within the interface
 *		frame.  All variables must be initialised!
 */
var interface_layer=null;
var history_layer=null;
var history_top=null;
var history_tab=null;
var history_left=null;
var history_right=null;
var history_top_right=null;
var history_up=null;
var history_down=null;
var history_view=null;
var history_frame=null;
var history_back=null;
var input_layer=null;
var input_top=null;
var input_bottom=null;
var input_left=null;
var input_right=null;
var input_top_left=null;
var input_top_right=null;
var input_bottom_left=null;
var input_bottom_right=null;
var input_view=null;
var input_box=null;
var input_question=null;
var input_answer=null;
var interface_document=null;
var client_document=null;
/*
 *		These variables allow us to keep track of timer events which control
 *		scrolling the history with the mouse, hiding the interface, minimising
 *		the interface, hiding the history display and polling the server.
 */
var scroll_timer=null;
var hide_timer=null;
var mini_timer=null;
var hist_timer=null;
var poll_timer=null;
var check_timer=null;
/*
 *		Initialise the interface by setting the short-cut variables, drawing the
 *		interface, showing it and setting up event signals.
 */
function init()
{
	interface_document=window.frames["InterfaceFrame"].document;
	client_document=window.frames["ClientFrame"].document;

	interface_layer=grab_layer("Interface");
	hide_layer(interface_layer);

	history_layer=grab_layer("History");
	history_tab=grab_layer("HistoryTab");
	history_top=grab_layer("HistoryTop");
	history_left=grab_layer("HistoryLeft");
	history_right=grab_layer("HistoryRight");
	history_top_right=grab_layer("HistoryTopRight");
	history_up=grab_layer("HistoryUp");
	history_down=grab_layer("HistoryDown");
	history_view=grab_layer("HistoryView");
	history_frame=grab_layer("HistoryFrame");
	history_back=grab_layer("HistoryBack");
	
	size_layer(history_tab,39,22);
	size_layer(history_top,1,13);
	size_layer(history_left,7,1);
	size_layer(history_right,20,1);
	size_layer(history_top_right,24,13);
	size_layer(history_up,9,8);
	size_layer(history_down,9,8);

	move_layer(history_top,history_tab.w,history_tab.h-history_top.h);
	move_layer(history_left,2,history_tab.h);
	move_layer(history_top_right,0,history_tab.h-history_top.h);
	move_layer(history_right,0,history_top_right.y+history_top_right.h);
	move_layer(history_up,0,history_right.y+arrow_offset);
	move_layer(history_back,history_left.x+history_left.w,
		history_top.y+history_top.h);
	move_layer(history_frame,history_back.x,history_back.y-3);
	move_layer(history_view,4,0);

	input_layer=grab_layer("Input");
	input_top=grab_layer("InputTop");
	input_bottom=grab_layer("InputBottom");
	input_left=grab_layer("InputLeft");
	input_right=grab_layer("InputRight");
	input_top_left=grab_layer("InputTopLeft");
	input_top_right=grab_layer("InputTopRight");
	input_bottom_left=grab_layer("InputBottomLeft");
	input_bottom_right=grab_layer("InputBottomRight");
	input_view=grab_layer("InputView");
	input_box=grab_layer("InputBox");
	input_question=grab_layer("Question");
	input_answer=grab_layer("Answer");

	size_layer(input_top,1,2);
	size_layer(input_bottom,1,7);
	size_layer(input_left,2,1);
	size_layer(input_right,6,1);
	size_layer(input_top_left,6,5);
	size_layer(input_top_right,8,9);
	size_layer(input_bottom_left,7,9);
	size_layer(input_bottom_right,8,10);
	size_layer(input_box,0,20);

	move_layer(input_top,input_top_left.w,0);
	move_layer(input_bottom,input_bottom_left.w,0);
	move_layer(input_left,0,input_top_left.h,0);
	move_layer(input_right,0,input_top_right.h,0);
	move_layer(input_view,input_left.w+4,input_top.h+4);
	move_layer(input_box,input_left.w+4,0);

	redraw(); redraw();
	toggle_history();

	show_layer(input_top);
	show_layer(input_bottom);
	show_layer(input_left);
	show_layer(input_right);
	show_layer(input_top_left);
	show_layer(input_top_right);
	show_layer(input_bottom_left);
	show_layer(input_bottom_right);
	show_layer(input_layer);
	show_layer(input_box);
	show_layer(input_question);
	show_layer(input_answer);
	show_layer(interface_layer);

	show_layer(history_tab);
	show_layer(history_top);
	show_layer(history_left);
	show_layer(history_right);
	show_layer(history_top_right);
	show_layer(history_up);
	show_layer(history_down);
	show_layer(history_back);
	show_layer(history_frame);
	show_layer(history_view);
	show_layer(history_layer);

	interface_document.getElementById("InputForm").onsubmit=answer_question;
	interface_document.getElementById("HistoryTab").ondblclick=toggle_history;
	interface_document.getElementById("HistoryUp").onmousedown=scroll_up;
	interface_document.getElementById("HistoryDown").onmousedown=scroll_down;
	interface_document.onkeypress=interface_key;
	interface_document.onkeydown=begin_scroll;
	window.frames["BrowserFrame"].focus();

	//check_browser();
	//set_poll_timer();
	//toggle_interface();
}
/*
 *		We want to fire up the interface whenever the user starts typing in the
 *		browser frame.  The event is cancelled whenever a new document is loaded
 *		in the browser frame.  We therefore re-instantiate this event whenever
 *		the interface is hidden, and at regular intervals.
 */
function check_browser()
{
	return;
	/*
	 *		In IE5, the interface frame annoyingly occasionally scrolls.
	 *		NB: This hack causes text to be entered backwards in IE5.5!
	 */
	/*
	if(window.frames["InterfaceFrame"].document.body.scrollTop!=0)
		window.frames["InterfaceFrame"].scrollTo(0,0);
	*/
	/*
	 *		This may get an "access denied" error message.
	 */
	if(check_timer!=null) window.clearTimeout(check_timer);
	window.frames["BrowserFrame"].document.onkeypress=browser_key;
	new_location=window.frames["BrowserFrame"].window.location.toString();
	if((old_location!=null)&&(new_location!=old_location))
	{
		client_document.getElementById("question").value="<CONTEXT:"+
			window.frames["BrowserFrame"].document.title+">";
		client_document.getElementById("query").submit();
	}
	old_location=new_location;
	check_timer=window.setTimeout(check_browser,check_timeout);
}
/*
 *		Redraw the interface to reflect any changes.
 */
function redraw()
{
	update_display_height();
	update_history_height();

	move_layer(interface_layer,interface_left,interface_top);
	size_layer(interface_layer,interface_width,interface_height);
	clip_layer(interface_layer);

	input_height=input_answer.h+input_question.h+17;

	size_layer(history_layer,interface_layer.w-history_inset*2,
		interface_layer.h-input_height);
	move_layer(history_layer,history_inset,0);
	clip_layer(history_layer);

	size_layer(history_top,history_layer.w-history_top.x-history_top_right.w,
		history_top.h);
	size_layer(history_left,history_left.w,history_layer.h-history_left.y);
	move_layer(history_right,history_layer.w-history_right.w-2,history_right.y);
	size_layer(history_right,history_right.w,history_layer.h-history_right.y);
	move_layer(history_top_right,history_layer.w-history_top_right.w,
		history_top_right.y);
	move_layer(history_up,history_right.x+history_right.w/2-history_up.w/2,
		history_up.y);
	move_layer(history_down,history_up.x,
		history_layer.h-history_down.h-arrow_offset);
	size_layer(history_back,history_right.x-history_back.x,
		history_layer.h-history_back.y);
	size_layer(history_frame,history_right.x-history_frame.x,
		history_layer.h-history_frame.y);
	clip_layer(history_frame);	
	size_layer(history_view,history_frame.w-8,0);

	size_layer(input_layer,interface_layer.w,input_height);
	clip_layer(input_layer);

	move_layer(input_top_right,input_layer.w-input_top_right.w,input_top.y);
	move_layer(input_bottom_right,input_layer.w-input_bottom_right.w,
		input_layer.h-input_bottom_right.h);
	move_layer(input_bottom_left,0,input_layer.h-input_bottom_left.h);
	size_layer(input_left,input_left.w,
		input_layer.h-input_top_left.h-input_bottom_left.h);
	size_layer(input_top,input_layer.w-input_top_left.w-input_top_right.w,
		input_top.h);
	move_layer(input_right,input_layer.w-input_right.w,input_right.y);
	size_layer(input_right,input_right.w,
		input_layer.h-input_top_right.h-input_bottom_right.h);
	move_layer(input_bottom,input_bottom.x,input_layer.h-input_bottom.h);
	size_layer(input_bottom,input_layer.w-input_bottom_left.w-input_bottom_right.w,
		input_bottom.h);
	move_layer(input_box,input_box.x,input_layer.h-input_bottom.h-input_box.h-4);
	size_layer(input_box,input_layer.w-input_left.w-input_right.w-8,input_box.h);
	size_layer(input_question,input_box.w,input_box.h);
	size_layer(input_answer,input_box.w,input_answer.h);
	clip_layer(input_answer);
}
/*
 *		Toggle display of the interface.  We do this simply by sizing the
 *		interface frame appropriately.
 */
function toggle_interface()
{
	return;
	if(interface_hidden==false)
	{
		document.getElementById("FrameSet").rows="100%,0";
		interface_hidden=true;
	}
	else
	{
		document.getElementById("FrameSet").rows="*,"+interface_layer.h+"px";
		interface_hidden=false;
		check_browser();
	}
	set_hide_timer();
	if(history_hidden==false) set_history_timer();
}
/*
 *		Toggle display of the history window.
 */
function toggle_history()
{
	move_layer(input_layer,0,history_layer.h);
	size_layer(interface_layer,interface_layer.w,input_layer.h+history_layer.h);
	clip_layer(interface_layer);
	return;
	if(history_hidden==false)
	{
		move_layer(input_layer,0,history_tab.h-3);
		size_layer(interface_layer,interface_layer.w,input_layer.h+input_layer.y);
		clip_layer(interface_layer);
		history_hidden=true;
		clear_history_timer();
	}
	else
	{
		move_layer(input_layer,0,history_layer.h);
		size_layer(interface_layer,interface_layer.w,input_layer.h+history_layer.h);
		clip_layer(interface_layer);
		history_hidden=false;
		set_history_timer();
	}
	set_hide_timer();
	return false;
}
/*
 *		Display a string in the interface.  This will usually be a reply from
 *		the server.  The height of the interface is dynamically recalculated to
 *		incorporate the string (the string may wrap, for example).
 */
function display_answer(text)
{
	if(!interface_document) return;
	paragraph=interface_document.getElementById("Answer").firstChild;
	paragraph.nodeValue=text;
	interface_minimised=false;
	redraw();
	toggle_history();
	set_minimise_timer();
}
function update_display_height()
{
	if(interface_minimised==false)
		input_answer.h=interface_document.getElementById("Answer").scrollHeight;
	else
		input_answer.h=0;
}
/*
 *		Display a string in the history window.  The type determines the
 *		appearance of the string according to the stylesheet.  The height of the
 *		history is dynamically recalculated to incorporate the string.
 */
function remember(type, text)
{
	if(!interface_document) return;
	if((!text)||(text.length==0)) text="(silence)";
	paragraph=
		interface_document.getElementById("HistoryView").lastChild.cloneNode(true);
	interface_document.getElementById("HistoryView").appendChild(paragraph);
	paragraph.firstChild.nodeValue=text;
	paragraph.attributes.item("id").nodeValue=type;
	redraw();
	update_history_height();
	pos=history_frame.h-history_view.h;
	if(pos<0) history_view.top=pos;
}
function update_history_height()
{
	history_view.h=interface_document.getElementById("HistoryView").scrollHeight;
}
/*
 *		If the user presses a key within the interface, the text box in the
 *		interface is activated.  However, if the escape key is pressed, then the
 *		interface is hidden.
 */
function interface_key()
{
	key=window.frames["InterfaceFrame"].window.event.keyCode;
	if(key==27) return action_hide();
	return activate_interface();
}
/*
 *		If the user presses a key within the browser frame, and if the user
 *		isn't typing into a text input at the time, then the text box in the
 *		interface is activated.  However, if the escape key is pressed, then the
 *		interface is hidden.  NB: We should also check for TEXTBOX, OBJECT, etc.
 */
function browser_key()
{
	target=window.frames["BrowserFrame"].window.event.srcElement.nodeName;
	key=window.frames["BrowserFrame"].window.event.keyCode;
	if(key==27) return action_hide();
	if(target=="INPUT") return true;
	/*
	 *		In IE5 the first keypress doesn't go through to the input box.
	 */
	if(browser_ie5)
	{
	}
	return activate_interface();
}
/*
 *		Show the interface, give the focus to the text input, and set up the
 *		various timer events.
 */
function activate_interface()
{
	window.frames["InterfaceFrame"].focus();
	interface_document.getElementById("Question").focus();
	set_hide_timer();
	set_history_timer();
	set_poll_timer();
	if(interface_hidden==true) toggle_interface();
	return true;
}
/*
 *		Scroll the contents of the history window via the keyboard, using the up
 *		and down arrows, page up and page down, and home and end.
 */
function begin_scroll()
{
	if(history_hidden==true) return true;
	interface_document.onkeyup=finish_scroll;
	key=window.frames["InterfaceFrame"].window.event.keyCode;
	switch(key)
	{
		case 38: key_speed=scroll_speed; break;
		case 40: key_speed=-scroll_speed; break;
		case 33: key_speed=history_frame.h; break;
		case 34: key_speed=-history_frame.h; break;
		case 36: key_speed=1000000; break;
		case 35: key_speed=-1000000; break;
		default: return true;
	}
	do_scroll();
	return false;
}
function do_scroll()
{
	if(key_speed>0)
	{
		history_view.top=parseInt(history_view.top)+key_speed;
		if(parseInt(history_view.top)>0) history_view.top=0;
	}
	else
	{
		update_history_height();
		pos=history_frame.h-history_view.h;
		if(pos<0)
		{
			history_view.top=parseInt(history_view.top)+key_speed;
			if(parseInt(history_view.top)<pos) history_view.top=pos;
		}
	}
	set_hide_timer();
	set_history_timer();
}
function finish_scroll()
{
	interface_document.onkeyup=null;
	return false;
}
/*
 *		Scroll the contents of the history window by clicking on the up and down
 *		arrows with the mouse.
 */
function scroll_up()
{
	key_speed=scroll_speed;
	do_scroll();
	interface_document.onmouseup=end_scroll;
	interface_document.getElementById("HistoryUp").onmouseout=end_scroll;
	scroll_timer=window.setTimeout(scroll_up,100);
	return false;
}
function scroll_down()
{
	key_speed=-scroll_speed;
	do_scroll();
	interface_document.onmouseup=end_scroll;
	interface_document.getElementById("HistoryDown").onmouseout=end_scroll;
	scroll_timer=window.setTimeout(scroll_down,100);
	return false;
}
function end_scroll()
{
	interface_document.onmouseup=null;
	interface_document.getElementById("HistoryUp").onmouseout=null;
	interface_document.getElementById("HistoryDown").onmouseout=null;
	window.clearTimeout(scroll_timer);
	return false;
}
/*
 *		Set and clear the timer events for minimising the interface, whereby the
 *		contents of the interface are concealed.
 */
function set_minimise_timer()
{
	return;
	clear_minimise_timer();
	mini_timer=window.setTimeout(minimise,mini_timeout);
}
function clear_minimise_timer()
{
	return;
	if(mini_timer!=null) window.clearTimeout(mini_timer);
	mini_timer=null;
}
function minimise()
{
	interface_minimised=true;
	clear_minimise_timer();
	redraw();
	key_speed=interface_document.getElementById("Answer").scrollHeight;
	do_scroll();
}
/*
 *		Set and clear the timer events for hiding the history window.
 */
function set_history_timer()
{
	return;
	clear_history_timer();
	hist_timer=window.setTimeout(histhide,hist_timeout);
}
function clear_history_timer()
{
	return;
	if(hist_timer!=null) window.clearTimeout(hist_timer);
	hist_timer=null;
}
function histhide()
{
	if(history_hidden==false) toggle_history();
	clear_history_timer();
}
/*
 *		Set and clear the timer events for hiding the interface.
 */
function set_hide_timer()
{
	return;
	clear_hide_timer();
	hide_timer=window.setTimeout(action_hide,hide_timeout);
}
function clear_hide_timer()
{
	return;
	if(hide_timer!=null) window.clearTimeout(hide_timer);
	hide_timer=null;
}
/*
 *		Set and clear the timer events for polling the server.
 */
function set_poll_timer()
{
	return;
	clear_poll_timer();
	poll_timer=window.setTimeout(poll_server,poll_timeout);
}
function clear_poll_timer()
{
	return;
	if(poll_timer!=null) window.clearTimeout(poll_timer);
	poll_timer=null;
}
function poll_server()
{
	client_document.getElementById("question").value="<POLL>";
	client_document.getElementById("query").submit();
	set_poll_timer();
}
/** Copyright (C) 2000 Artificial Intelligence NV **/
