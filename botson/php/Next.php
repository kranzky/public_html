<?
/*============================================================================*/
	/*
	 *
	 */
	function NextAdd($id)
	{
		global $task;
		global $next_add;
		global $sub_rule;
		if($task=="view")
		{
			$sub_rule=$next_add;
			RuleSubView();
			return;
		}
		if(!AuthorEnsure()) return;
		$type=RuleType($id);
		if(($type=="Goal")||($type=="Fail"))
		{
			trigger_error("beware that adding expectations to goal-oriented ".
				"rules is pointless",GOOD);
		}
		$result=mysql_query("INSERT INTO RuleNext (RuleID,NextID) ".
			" VALUES (".$id.",".$next_add.")")
			or trigger_error("you cannot add the same expectation twice",EVIL);
	}
/*============================================================================*/
	/*
	 *
	 */
	function NextAddRule($id)
	{
		global $next_add;
		$parent=$next_add;
		$next_add=RuleAdd($id);
		NextAdd($parent);
	}
/*============================================================================*/
	/*
	 *
	 */
	function NextSubView($id)
	{
		global $task;
		global $sub_next;
		global $sub_rule;
		global $action;
		if($task=="view")
		{
			/*
			$response=mysql_query("SELECT NextID FROM RuleNext WHERE RuleID=".$id)
				or trigger_error("unable to select",STOP);
			$row=mysql_fetch_row($response)
				or trigger_error("nothing selected",STOP);
			*/
			$sub_rule=$sub_next;
			RuleSubView();
			return;
		}
		if($sub_next==0) return;
		$action="NextConfirmDelete(".$sub_next.")";
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function NextConfirmDelete($id)
	{
		if(!AuthorEnsure()) return;
		$result=mysql_query("SELECT RuleID,NextID FROM RuleNext WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
?>
<H2>are you sure you want to delete the expectation</H2>
<P>Please confirm that you want to delete the expectation from rule
"<? echo RuleName($row[0]); ?>" ro rule "<? echo RuleName($row[1]); ?>".</P>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" NAME="confirm" VALUE="no, do not delete the expectation">
<INPUT TYPE="submit" NAME="confirm" VALUE="yes, delete the expectation">
<INPUT TYPE="hidden" NAME="perform" VALUE="NextDelete(<? echo $id; ?>)">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function NextDelete($id)
	{
		global $confirm;
		if(!AuthorEnsure()) return;
		if($confirm=="yes, delete the expectation")
		{
			$result=mysql_query("DELETE FROM RuleNext WHERE ID=".$id);
			if(!$result)
			{
				trigger_error("you tried deleting a non-existent expectation",EVIL);
				return;
			}
			trigger_error("you deleted the expectation",GOOD);
		} else {
			trigger_error("expectation not deleted",GOOD);
		}
	}
/*============================================================================*/
?>
