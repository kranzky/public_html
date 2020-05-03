<?
/*============================================================================*/
	/*
	 *		Display the top-level context menu which changes depending on the
	 *		type of the current user.
	 */
	function InterfaceMain()
	{
		global $author_id;
		global $author_type;
		global $author_name;
?>
<H2>main menu
<?
	if($author_id!=0)
	{
?>
(<SMALL>signed on as "<? echo $author_name; ?>"</SMALL>)
<?
	} else {
		echo "(<SMALL>not currently signed on</SMALL>)";
	}
?>
</H2>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="action">
<OPTION SELECTED VALUE="">please select a task</OPTION>
<? if($author_type=="Admin") { ?>
<OPTION VALUE="AuthorAdd">add an author</OPTION>
<OPTION VALUE="AuthorEdit">edit an author</OPTION>
<? } if(($author_type=="Author")||($author_type=="Admin")) { ?>
<OPTION VALUE="AuthorUpdate(<? echo $author_id; ?>)">update your profile</OPTION>
<OPTION VALUE="SessionLogout">finish your authoring session</OPTION>
<? } if($author_type=="Visitor") { ?>
<OPTION VALUE="SessionLogin">begin your authoring session</OPTION>
<? } ?>
</SELECT>
<INPUT TYPE="submit" VALUE="perform task">
</FORM>
<?
	}
/*============================================================================*/
	function InterfaceView($data)
	{
		global $view;
		$view=$data;
	}
/*============================================================================*/
	/*
	 *		If no action is performed we display the main interface, allowing
	 *		user's to navigate the system and perform various actions depending
	 *		on their authority.
	 */
	function InterfaceBody()
	{
	}
/*============================================================================*/
	/*
	 *		Execute a command.
	 */
	function InterfaceExecute($data,$show=true)
	{
		$array=explode("(",$data);
		$function=$array[0];
		$array=explode(")",$array[1]);
		$argument=$array[0];
		if($function!="")
		{
			$function($argument);
		}
	}
/*============================================================================*/
?>
