<?
/*============================================================================*/
	/*
	 *
	 */
	function EntryEdit($id)
	{
		if(!AuthorEnsure()) return;
		$result=mysql_query("SELECT Pattern,RuleID FROM RuleEntry ".
			"WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if(!$row)
		{
			trigger_error("there is no such entry point",EVIL);
			return;
		}
		if(!DatabaseLock("Rule","WHERE ID=".$row[1])) return;
?>
<H2>update entry point information</H2>
<P>Please edit the information shown in the form below and click on
"update entry point information" to execute the changes.</P>
<FORM METHOD="Post" ACTION="Index.html">
Entry Point Pattern:
<INPUT SIZE="60" NAME="update_entry[pattern]" VALUE="<? echo $row[0]; ?>"><BR>
<INPUT TYPE="submit" VALUE="update entry point information">
<INPUT TYPE="hidden" NAME="update_entry[rule_id]" VALUE=<? echo $row[1]; ?>>
<INPUT TYPE="hidden" NAME="update_entry[id]" VALUE=<? echo $id; ?>>
<INPUT TYPE="hidden" NAME="perform" VALUE="EntryPerformUpdate">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function EntrySubEdit()
	{
		global $task;
		global $sub_entry;
		global $action;
		if($task=="edit")
		{
			$action="EntryEdit(".$sub_entry.")";
			return;
		}
		if($sub_entry==0) return;
		$action="EntryConfirmDelete(".$sub_entry.")";
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function EntryPattern($id)
	{
		$result=mysql_query("SELECT Pattern FROM RuleEntry WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if($row) return $row[0];
		return "Unnamed entry point";
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function EntryConfirmDelete($id)
	{
		if(!AuthorEnsure()) return;
?>
<H2>are you sure you want to delete the entry point</H2>
<P>Please confirm that you want to delete the entry point
"<? echo EntryPattern($id); ?>".</P>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" NAME="confirm" VALUE="no, do not delete the entry point">
<INPUT TYPE="submit" NAME="confirm" VALUE="yes, delete the entry point">
<INPUT TYPE="hidden" NAME="perform" VALUE="EntryDelete(<? echo $id; ?>)">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function EntryDelete($id)
	{
		global $confirm;
		if(!AuthorEnsure()) return;
		if($confirm=="yes, delete the entry point")
		{
			$result=mysql_query("DELETE FROM RuleEntry WHERE ID=".$id);
			if(!$result)
			{
				trigger_error("you tried deleting a non-existent entry point",EVIL);
				return;
			}
			trigger_error("you deleted the entry point",GOOD);
		} else {
			trigger_error("entry point not deleted",GOOD);
		}
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function EntryPerformUpdate()
	{
		global $author_id;
		global $update_entry;
		global $rule_id;
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Rule","WHERE ID=".$update_entry["rule_id"])) return;
		$result=mysql_query("UPDATE RuleEntry SET Pattern=\"".
			$update_entry["pattern"]."\" WHERE ID=".$update_entry["id"])
			or trigger_error("unable to update entry point",STOP);
		DatabaseUnlock("Rule","WHERE ID=".$update_entry["rule_id"]);
		trigger_error("the entry point has been updated",GOOD);
		$rule_id=$update_entry["rule_id"]; // view once edit over
		InterfaceView("RuleShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function EntryAdd($id)
	{
		global $author_id;
		global $action;
		if(!AuthorEnsure()) return;
		$result=mysql_query("INSERT INTO RuleEntry (RuleID,Pattern) ".
			" VALUES (".$id.",\"New entry point\")")
			or trigger_error("unable to create new entry",STOP);
		$child=mysql_insert_id();
		$action="EntryEdit(".$child.")";
	}
/*============================================================================*/
?>
