<?
/*============================================================================*/
	/*
	 *		Recover information about the current session, or create a new
	 *		session if none exists already.
	 */
	function SessionRecover()
	{
		global $botson_session;
		global $class_id;
		global $script_id;
		global $rule_id;
		global $view;
		global $author_id;
		global $UNIQUE_ID;
		if(isset($botson_session))
		{
			$result=mysql_query("SELECT AuthorID FROM Session ".
				"WHERE Session=\"".$botson_session."\"")
				or trigger_error("i was unable to get the author id",STOP);
			if($row=mysql_fetch_row($result))
			{
				if($row[0]!=0) $author_id=$row[0];
			} else {
				trigger_error("unable to recover session",EVIL);
				unset($botson_session);
				session_unset();
			}
		}
		if(!isset($botson_session))
		{
			session_register("botson_session");
			$botson_session=$UNIQUE_ID;
			session_register("class_id");
			$class_id=1;
			session_register("script_id");
			$script_id=0;
			session_register("rule_id");
			$rule_id=0;
			session_register("view");
			$view="ClassShow";
			$result=mysql_query("INSERT INTO Session (Session,AuthorID,Created) ".
				"VALUES (\"".$botson_session."\",0,NOW())")
				or trigger_error("unable to insert session",STOP);
		}
	}
/*============================================================================*/
	/*
	 *		Provide a form for the user to log in to the system.
	 */
	function SessionLogin()
	{
		$result=mysql_query("SELECT ID,Name FROM Author")
			or trigger_error("unable to select author names",STOP);
?>
<H2>start your authoring session</H2>
<P>To start your session please select your name from the
menu shown below, enter your password, and then click on the
"start session" button.</P>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="login[id]">
<OPTION SELECTED VALUE="0">please select your name</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=\"".$row[0]."\">".$row[1]."</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="password" SIZE="16" NAME="login[password]">
<INPUT TYPE="submit" VALUE="start session">
<INPUT TYPE="hidden" NAME="process" VALUE="SessionProcessLogin">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *		Process the attempt to log in, and either grant or deny access.
	 */
	function SessionProcessLogin()
	{
		global $botson_session;
		global $author_id;
		global $login;
		/*
		 *		See whether an author is already logged in.
		 */
		$result=mysql_query("SELECT AuthorID FROM Session WHERE Session=\"".
			$botson_session."\"")
			or trigger_error("unable to select from session",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("current session not found in table",STOP);
		if($row[0]!=0)
		{
			trigger_error("i have already started an authoring session",EVIL);
			return;
		}
		/*
		 *		Check to see whether the supplied name and password are valid.
		 */
		$result=mysql_query("SELECT ID FROM Author WHERE ID=\"".
			$login["id"]."\" AND Password=\"".$login["password"]."\"")
			or trigger_error("unable to select author from table",STOP);
		$row=mysql_fetch_row($result);
		if(!$row)
		{
			trigger_error("the password you sent me isn't the right one",EVIL);
			return;
		}
		$author_id=$row[0];
		/*
		 *		Enter the author identifier into the session database.
		 */
		$result=mysql_query("UPDATE Session SET AuthorID=".$author_id." ".
			"WHERE Session=\"".$botson_session."\"")
			or trigger_error("unable to insert into table",STOP);
		trigger_error("it is my awesome pleasure to welcome you to a ".
			"new authoring session",GOOD);
	}
/*============================================================================*/
	/*
	 *		Confirm with the user their decision to logout.
	 */
	function SessionLogout()
	{
?>
<H2>are you sure you want to end your authoring session</H2>
<P>Please confirm that you do indeed wish to end your session.</P>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" NAME="logout" VALUE="no, do not end my session">
<INPUT TYPE="submit" NAME="logout" VALUE="yes, please end my session">
<INPUT TYPE="hidden" NAME="process" VALUE="SessionProcessLogout">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *		Actually log the current user out of the system.
	 */
	function SessionProcessLogout()
	{
		global $botson_session;
		global $author_id;
		global $logout;
		if($logout!="yes, please end my session")
		{
			trigger_error("i have allowed your session to continue",GOOD);
			return;
		}
		$result=mysql_query("UPDATE Session SET AuthorID=0 ".
			"WHERE Session=\"".$botson_session."\"")
			or trigger_error("unable to update database",STOP);
		trigger_error("you have finished your authoring session",GOOD);
		unset($author_id);
	}
/*============================================================================*/
?>
