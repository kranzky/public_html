<?
/*============================================================================*/
	/*
	 *		Retrieve the name and type of the author corresponding to the
	 *		global author_id variable.
	 */
	function AuthorRetrieve()
	{
		global $author_id;
		global $author_name;
		global $author_type;
		if(!isset($author_id))
		{
			$author_name = "friend";
			$author_type = "Visitor";
		} else {
			$result=mysql_query("SELECT Name,Type FROM Author ".
				"WHERE ID=".$author_id)
				or trigger_error("unable to select author info",STOP);
			$row=mysql_fetch_row($result)
				or trigger_error("author id set but no author",STOP);
			$author_name = $row[0];
			$author_type = $row[1];
		}
	}
/*============================================================================*/
	/*
	 *		Return a string containing the name of the specified author.
	 */
	function AuthorName($id)
	{
		$result=mysql_query("SELECT Name FROM Author WHERE ID=".$id)
			or trigger_error("unable to select author name",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("no author for specified id",STOP);
		return $row[0];
	}
/*============================================================================*/
	/*
	 *		Allow the current author to add a new author to the system.  Display
	 *		a form allowing the current author to specify the name and password
	 *		of the new author.
	 */
	function AuthorAdd()
	{
		global $author_name;
		global $author_type;
		global $author_id;
		if(!AdminEnsure()) return;
?>
<H2>add an author</H2>
<P>Hello <? echo $author_name; ?>, oh great one!  You have so much power,
but please use it wisely!  You may add an author by filling out this form
with the author's details and then clicking the "add author" button.</P>
<FORM METHOD="Post" ACTION="Index.html">
Author's name: <INPUT SIZE="32" NAME="new_author[name]"><BR>
Author's password: <INPUT TYPE="password" SIZE="16" NAME="new_author[password]"><BR>
<INPUT TYPE="submit" VALUE="add author">
<INPUT TYPE="hidden" NAME="perform" VALUE="AuthorPerformAdd">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *		Actually perform the operation of adding a new author to the system.
	 *		A new author of the specified name and password is created with other
	 *		values set to their defaults.
	 */
	function AuthorPerformAdd()
	{
		global $author_type;
		global $author_id;
		global $new_author;
		if(!AdminEnsure()) return;
		$result=mysql_query("SELECT * FROM Author WHERE Name=\"".
			$new_author["name"]."\"")
			or trigger_error("unable to select from table",STOP);
		if(mysql_num_rows($result)!=0)
		{
			trigger_error("an author called \"".$new_author["name"]."\" is ".
				"already a member",EVIL);
			return;
		}
		$result=mysql_query("INSERT INTO Author ".
			"(Name,Password,Type,CreateID,Created) ".
			"VALUES (\"".$new_author["name"]."\",\"".$new_author["password"].
			"\",'Author',".$author_id.",NOW())")
			or trigger_error("unable to insert into table",STOP);
		trigger_error("i quiver with excitement to inform you that i have ".
			"added an author named \"".$new_author["name"]."\"",GOOD);
	}
/*============================================================================*/
	/*
	 *		Provide a form containing the details of the author specified in
	 *		the argument, allowing the current author to modify those details.
	 */
	function AuthorUpdate($id)
	{
		global $author_type;
		global $author_id;
		if(!AuthorEnsure($id)) return;
		if(!DatabaseLock("Author","WHERE ID=".$id)) return;
		$result=mysql_query("SELECT Name,Email,Phone,Password,Description ".
			"FROM Author WHERE ID=".$id)
			or trigger_error("unable to select author details",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
?>
<H2>update author profile</H2>
<P>Please edit the information shown in the form below and click on
"update author profile" to execute the changes.</P>
<FORM METHOD="Post" ACTION="Index.html">
Author's name:
<INPUT SIZE="32" NAME="update_author[name]" VALUE="<? echo $row[0]; ?>"><BR>
Author's password:
<INPUT TYPE="password" SIZE="16" NAME="update_author[password]"
	VALUE="<? echo $row[3]; ?>"><BR>
Author's Email:
<INPUT SIZE="32" NAME="update_author[email]" VALUE="<? echo $row[1]; ?>"><BR>
Author's Phone:
<INPUT SIZE="16" NAME="update_author[phone]" VALUE="<? echo $row[2]; ?>"><BR>
Author's Description:<BR>
<TEXTAREA NAME="update_author[description]" ROWS=10 COLS=60>
<? echo $row[4]; ?></TEXTAREA><BR>
<INPUT TYPE="submit" VALUE="update author profile">
<INPUT TYPE="hidden" NAME="update_author[id]" VALUE=<? echo $id; ?>>
<INPUT TYPE="hidden" NAME="perform" VALUE="AuthorPerformUpdate">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *		Update the details of an author based upon edits performed by the
	 *		current author.
	 */
	function AuthorPerformUpdate()
	{
		global $author_id;
		global $update_author;
		if(!AuthorEnsure($id)) return;
		if(!DatabaseLock("Author","WHERE ID=".$update_author["id"])) return;
		$result=mysql_query("UPDATE Author SET Name=\"".$update_author["name"].
			"\",Password=\"".$update_author["password"]."\",Email=\"".
			$update_author["email"]."\",Phone=\"".$update_author["phone"].
			"\",Description=\"".$update_author["description"]."\",".
			"ChangeID=".$author_id." WHERE ID=".$update_author["id"])
			or trigger_error("unable to update author",STOP);
		DatabaseUnlock("Author","WHERE ID=".$update_author["id"]);
		trigger_error("the profile of ".$update_author["name"]." has been ".
			"updated",GOOD);
	}
/*============================================================================*/
	/*
	 *		Allow an administrator to select an author to edit from a menu.
	 */
	function AuthorEdit()
	{
		global $author_type;
		global $author_id;
		if(!AdminEnsure()) return;
		$result=mysql_query("SELECT ID,Name FROM Author")
			or trigger_error("unable to select author names",STOP);
?>
<H2>edit author</H2>
<P>Select the author you would like to edit from the menu shown below, then
click on "edit author" to edit that author.</P>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="author_edit">
<OPTION SELECTED VALUE="0">please select an author</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=\"".$row[0]."\">".$row[1]."</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="edit author">
<INPUT TYPE="hidden" NAME="process" VALUE="AuthorProcessEdit">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *		When the administrator has selected an author to edit we simply
	 *		transform their selection into the appropriate editing command.
	 */
	function AuthorProcessEdit()
	{
		global $author_edit;
		global $action;
		$action="AuthorUpdate(".$author_edit.")";
	}
/*============================================================================*/
	function AuthorEnsure()
	{
		global $author_type;
		global $author_id;
		if(($author_type!="Admin")&&($author_type!="Author"))
		{
			trigger_error("you need to have author priveleges to do that",EVIL);
			return false;
		}
		return true;
	}
/*----------------------------------------------------------------------------*/
	function AdminEnsure($id=0)
	{
		global $author_type;
		global $author_id;
		if(($id==0)&&($author_type!="Admin"))
		{
			trigger_error("you need to have admin priveleges to do that",EVIL);
			return false;
		}
		if(($id!=0)&&($author_type!="Admin")&&($author_id!=$id))
		{
			trigger_error("you need to have admin priveleges or you need to be ".
				AuthorName($id)." to do that",EVIL);
			return false;
		}
		return true;
	}
/*----------------------------------------------------------------------------*/
	function AuthorEnsureQuiet()
	{
		global $author_type;
		global $author_id;
		if(($author_type!="Admin")&&($author_type!="Author"))
		{
			return false;
		}
		return true;
	}
/*----------------------------------------------------------------------------*/
	function AdminEnsureQuiet($id=0)
	{
		global $author_type;
		global $author_id;
		if(($id==0)&&($author_type!="Admin"))
		{
			return false;
		}
		if(($id!=0)&&($author_type!="Admin")&&($author_id!=$id))
		{
			return false;
		}
		return true;
	}
/*============================================================================*/
?>
