/** Copyright (C) 2000 Artificial Intelligence NV **/
/*
 *		The client frame contains a form which is used to submit a request to
 *		the server.  The interface frame needs to retrieve the information from
 *		the client frame once the form has been submitted.  We therefore call
 *		a function in the interface frame whenever a page is loaded in the
 *		client frame.
 */
onload = extract_reply;
function extract_reply()
{
	child=document.getElementById("response").childNodes;
	action=new Array();
	machine="";
	for(i=0; i<child.length; ++i)
	{
		if(child.item(i).nodeName=="ACTION")
		{
			action[action.length]=child.item(i+1).nodeValue;
			i+=2;
		}
		else if(child.item(i).nodeName=="#text")
		{
			machine+=child.item(i).nodeValue;
		}
		else
		{
			machine+=child.item(i).nodeName;
		}
	}
	parent.server_reply(machine,action);
	return false;
}
/** Copyright (C) 2000 Artificial Intelligence NV **/
