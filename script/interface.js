/*
 *		Hook up the program to the chatbot (load simple PHP code in a frame, use javascript to fill in and submit
 *			or may use a Java class, or may use the Location object).
 *		Add capabilities to the chatbot to initiate actions with a special syntax.
 *		Allow the user to scroll the history window with keyboard.
 *		Allow the user to show/hide the interface with a hotkey.
 *		Make the input box auto-size to fit in the display text, and make history scroll stop at top and bottom.
 *		Make interface appear in a new window?
 *		Clean up the code so that we don't have so many global variables (use objects?)
 *		Test on other versions of IE5 and Nav6, and put warnings for other browsers which may not work.
 *		Eventually port to IE4 and Nav4 (and Opera).
 */

onload = init;

var history_hidden=false;
var arrow_offset=10;
var history_inset=10;
var interface_left=200;
var interface_top=100;
var interface_width=500;
var interface_height=300;
var input_height=100;

var interface_layer;

var history_layer;
var history_top;
var history_tab;
var history_left;
var history_right;
var history_top_right;
var history_up;
var history_down;
var history_view;
var history_frame;

var input_layer;
var input_top;
var input_bottom;
var input_left;
var input_right;
var input_top_left;
var input_top_right;
var input_bottom_left;
var input_bottom_right;
var input_view;
var input_box;
var input_question;
var input_answer;

var scroll_timer=null;

function grab_layer(name)
{
	my_layer=document.getElementById(name).style;
	my_layer.position="absolute";
	my_layer.display="block";
	move_layer(my_layer,0,0);
	size_layer(my_layer,0,0);
	hide_layer(my_layer);
	return my_layer;
}

function size_layer(my_layer,width,height)
{
	my_layer.width=width;
	my_layer.height=height;
	my_layer.w=width;
	my_layer.h=height;
}

function move_layer(my_layer,left,top)
{
	my_layer.left=left;
	my_layer.top=top;
	my_layer.x=left;
	my_layer.y=top;
}

function show_layer(my_layer)
{
	my_layer.visibility="visible";
}

function hide_layer(my_layer)
{
	my_layer.visibility="hidden";
}

function clip_layer(my_layer)
{
	my_layer.clip="rect(0,"+my_layer.w+","+my_layer.h+",0)";
}

function init()
{
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
	move_layer(history_frame,history_left.x+history_left.w,history_top.y+history_top.h);
	move_layer(history_view,4,4);

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

	redraw();

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
	show_layer(history_frame);
	show_layer(history_view);
	show_layer(history_layer);

	/*
	 *		Netscape may require:
	 *
	 *		- document.getElementById("HistoryTab").captureEvents(Event.DBLCLICK);
	 *		- document.getElementById("HistoryUp").captureEvents(Event.MOUSEDOWN|event.MOUSEUP);
	 */
	document.getElementById("InputForm").onsubmit=answer_question;
	document.getElementById("HistoryTab").ondblclick=toggle_history;
	document.getElementById("HistoryTab").onmousedown=begin_drag;
	document.getElementById("HistoryUp").onmousedown=scroll_up;
	document.getElementById("HistoryDown").onmousedown=scroll_down;
}

function test_it()
{
	alert(event.width+", "+event.height);
}

function begin_drag()
{
	document.onmousemove=do_drag;
	document.onmouseup=end_drag;
	return false;
}
function do_drag()
{
	if(history_hidden==true)
		move_layer(interface_layer,event.x-history_tab.w/2-history_inset,event.y-history_layer.h+history_tab.h/2);
	else
		move_layer(interface_layer,event.x-history_tab.w/2-history_inset,event.y-history_tab.h/2);
	return false;
}
function end_drag()
{
	document.onmousemove=null;
	document.onmouseup=null;
	return false;
}

function scroll_up()
{
	history_view.top=parseInt(history_view.top)+10;
	document.onmouseup=end_scroll;
	document.getElementById("HistoryUp").onmouseout=end_scroll;
	scroll_timer=window.setTimeout(scroll_up,100);
	return false;
}
function scroll_down()
{
	history_view.top=parseInt(history_view.top)-10;
	document.onmouseup=end_scroll;
	document.getElementById("HistoryDown").onmouseout=end_scroll;
	scroll_timer=window.setTimeout(scroll_down,100);
	return false;
}
function end_scroll()
{
	document.onmouseup=null;
	document.getElementById("HistoryUp").onmouseout=null;
	document.getElementById("HistoryDown").onmouseout=null;
	window.clearTimeout(scroll_timer);
	return false;
}

function answer_question()
{
	user=document.getElementById("Question").value;
	document.getElementById("Question").value="";
	machine="You said \""+user+"\".";
	display_answer(machine);
	remember("User", user);
	remember("Machine", machine);
	return false;
}

function redraw()
{
	move_layer(interface_layer,interface_left,interface_top);
	size_layer(interface_layer,interface_width,interface_height);
	clip_layer(interface_layer);

	size_layer(history_layer,interface_layer.w-history_inset*2,interface_layer.h-input_height);
	move_layer(history_layer,history_inset,0);
	clip_layer(history_layer);

	size_layer(history_top,history_layer.w-history_top.x-history_top_right.w,history_top.h);
	size_layer(history_left,history_left.w,history_layer.h-history_left.y);
	move_layer(history_right,history_layer.w-history_right.w-2,history_right.y);
	size_layer(history_right,history_right.w,history_layer.h-history_right.y);
	move_layer(history_top_right,history_layer.w-history_top_right.w,history_top_right.y);
	move_layer(history_up,history_right.x+history_right.w/2-history_up.w/2,history_up.y);
	move_layer(history_down,history_up.x,history_layer.h-history_down.h-arrow_offset);
	size_layer(history_frame,history_right.x-history_frame.x,history_layer.h-history_frame.y);
	clip_layer(history_frame);	
	size_layer(history_view,history_frame.w-8,0);

	size_layer(input_layer,interface_layer.w,input_height);
	move_layer(input_layer,0,history_layer.h);
	clip_layer(input_layer);

	move_layer(input_top_right,input_layer.w-input_top_right.w,input_top.y);
	move_layer(input_bottom_right,input_layer.w-input_bottom_right.w,input_layer.h-input_bottom_right.h);
	move_layer(input_bottom_left,0,input_layer.h-input_bottom_left.h);
	size_layer(input_left,input_left.w,input_layer.h-input_top_left.h-input_bottom_left.h);
	size_layer(input_top,input_layer.w-input_top_left.w-input_top_right.w,input_top.h);
	move_layer(input_right,input_layer.w-input_right.w,input_right.y);
	size_layer(input_right,input_right.w,input_layer.h-input_top_right.h-input_bottom_right.h);
	move_layer(input_bottom,input_bottom.x,input_layer.h-input_bottom.h);
	size_layer(input_bottom,input_layer.w-input_bottom_left.w-input_bottom_right.w,input_bottom.h);
	move_layer(input_box,input_box.x,input_layer.h-input_bottom.h-input_box.h-4);
	size_layer(input_box,input_layer.w-input_left.w-input_right.w-8,input_box.h);
	size_layer(input_question,input_box.w,input_box.h);
	size_layer(input_answer,input_box.w,input_box.h);
}

function display_answer(text)
{
	paragraph=document.getElementById("Answer").firstChild;
	paragraph.nodeValue=text;
}

function remember(type, text)
{
	paragraph=document.getElementById("HistoryView").lastChild.cloneNode(true);
	document.getElementById("HistoryView").appendChild(paragraph);
	paragraph.firstChild.nodeValue=text;
	/*
	 *		W3C specifications suggest this should be paragraph.attributes.getNamedItem("id").nodeValue,
	 *		but IE5.5 under 2000 gives an error on this.
	 */
	paragraph.attributes.item("id").nodeValue=type;
}

function toggle_history()
{
	if(history_hidden==true)
	{
		move_layer(history_layer,history_layer.x,history_layer.y-history_layer.h+history_tab.h-3);
		if(interface_layer.x<0) move_layer(interface_layer,0,interface_layer.y);
		if(interface_layer.y<0) move_layer(interface_layer,interface_layer.x,0);
		history_hidden=false;
	}
	else
	{
		move_layer(history_layer,history_layer.x,history_layer.y+history_layer.h-history_tab.h+3);
		history_hidden=true;
	}
	return false;
}
