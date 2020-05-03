<?
/*============================================================================*/
	/*
	 *
	 */
	function ResponseEdit($id)
	{
		if(!AuthorEnsure()) return;
		$result=mysql_query("SELECT Response,RuleID FROM RuleResponse ".
			"WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if(!$row)
		{
			trigger_error("there is no such response",EVIL);
			return;
		}
		if(!DatabaseLock("Rule","WHERE ID=".$row[1])) return;
?>
<H2>update response information</H2>
<P>Please edit the information shown in the form below and click on
"update response information" to execute the changes.</P>
<FORM METHOD="Post" ACTION="Index.html">
Response:
<INPUT SIZE="60" NAME="update_response[response]" VALUE="<? echo $row[0]; ?>"><BR>
<INPUT TYPE="submit" VALUE="update response information">
<INPUT TYPE="hidden" NAME="update_response[rule_id]" VALUE=<? echo $row[1]; ?>>
<INPUT TYPE="hidden" NAME="update_response[id]" VALUE=<? echo $id; ?>>
<INPUT TYPE="hidden" NAME="perform" VALUE="ResponsePerformUpdate">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ResponseSubEdit()
	{
		global $task;
		global $sub_response;
		global $action;
		if($task=="edit")
		{
			$action="ResponseEdit(".$sub_response.")";
			return;
		}
		if($sub_response==0) return;
		$action="ResponseConfirmDelete(".$sub_response.")";
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ResponseResponse($id)
	{
		$result=mysql_query("SELECT Response FROM RuleResponse WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if($row) return $row[0];
		return "Unnamed response";
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ResponseConfirmDelete($id)
	{
		if(!AuthorEnsure()) return;
?>
<H2>are you sure you want to delete the response</H2>
<P>Please confirm that you want to delete the response
"<? echo ResponseResponse($id); ?>".</P>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" NAME="confirm" VALUE="no, do not delete the response">
<INPUT TYPE="submit" NAME="confirm" VALUE="yes, delete the response">
<INPUT TYPE="hidden" NAME="perform" VALUE="ResponseDelete(<? echo $id; ?>)">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ResponseDelete($id)
	{
		global $confirm;
		if(!AuthorEnsure()) return;
		if($confirm=="yes, delete the response")
		{
			$result=mysql_query("DELETE FROM RuleResponse WHERE ID=".$id);
			if(!$result)
			{
				trigger_error("you tried deleting a non-existent response",EVIL);
				return;
			}
			trigger_error("you deleted the response",GOOD);
		} else {
			trigger_error("response not deleted",GOOD);
		}
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ResponsePerformUpdate()
	{
		global $author_id;
		global $update_response;
		global $rule_id;
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Rule","WHERE ID=".$update_response["rule_id"])) return;
		$result=mysql_query("UPDATE RuleResponse SET Response=\"".
			$update_response["response"]."\" WHERE ID=".$update_response["id"])
			or trigger_error("unable to update response",STOP);
		DatabaseUnlock("Rule","WHERE ID=".$update_response["rule_id"]);
		trigger_error("the response has been updated",GOOD);
		$rule_id=$update_response["rule_id"]; // view once edit over
		InterfaceView("RuleShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function ResponseAdd($id)
	{
		global $author_id;
		global $action;
		if(!AuthorEnsure()) return;
		$result=mysql_query("INSERT INTO RuleResponse (RuleID,Response) ".
			" VALUES (".$id.",\"New response\")")
			or trigger_error("unable to create new response",STOP);
		$child=mysql_insert_id();
		$action="ResponseEdit(".$child.")";
	}
/*============================================================================*/
?>
