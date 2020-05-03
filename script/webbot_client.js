/** Copyright (C) 2000 Artificial Intelligence NV **/
/*
 *		These functions are allow the interface to interact with the server.
 *		The first function fills in and submits the form in the hidden client
 *		frame, while the second function is automatically called whenever the
 *		client frame is loaded.  This function extracts results from the client
 *		frame, and uses the API to perform appropriate actions.
 */
function answer_question()
{
	set_poll_timer();
	user=interface_document.getElementById("Question").value;
	interface_document.getElementById("Question").value="";
	remember("User", user);
	client_document.getElementById("question").value=user;
	client_document.getElementById("query").submit();
	return false;
}
function server_reply(machine,action)
{
	/*
	 *		IE5 loses client_document when the frame is re-loaded, so we
	 *		re-initialise it here.
	 */
	client_document=window.frames["ClientFrame"].document;
	alert("XXX");
	if(machine!="")
	{
		alert("YYY");
		display_answer(machine);
		remember("Machine", machine);
	}
	for(i=0; i<action.length; ++i)
		perform_action(action[i]);
}
/** Copyright (C) 2000 Artificial Intelligence NV **/
