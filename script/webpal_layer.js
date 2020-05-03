/** Copyright (C) 2000 Artificial Intelligence NV **/
/*
 *		This file contains some fairly generic layer manipulation functions
 *		which are used to create the interface.
 */
function grab_layer(name)
{
	my_layer=interface_document.getElementById(name).style;
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
/** Copyright (C) 2000 Artificial Intelligence NV **/
