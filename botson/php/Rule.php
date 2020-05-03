<?
/*============================================================================*/
	/*
	 *
	 */
	function RuleShow()
	{
		global $rule_id;
		$result=mysql_query("SELECT Label,Description,ParentID,Type ".
			"FROM Rule WHERE ID=".$rule_id)
			or trigger_error("select failed",STOP);
		$row=mysql_fetch_row($result);
		if(!$row)
		{
			trigger_error("no such rule exists",EVIL);
			InterfaceView("ScriptShow");
			ScriptShow();
			return;
		}
		$rulename=$row[0];
		$parent_id=$row[2];
?>
<H2>rule view: <? echo $rulename; ?></H2>
<P><? echo $row[1]; ?></P>
<TABLE>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="edit this rule">
<INPUT TYPE="hidden" NAME="action" VALUE="RuleEdit(<? echo $rule_id; ?>)">
</FORM>
</TD>
<?
	}
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="run thread">
<INPUT TYPE="hidden" NAME="action" VALUE="ScriptRun(<? echo $parent_id; ?>)">
</FORM>
</TD>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="export in SEPO format">
<INPUT TYPE="hidden" NAME="action" VALUE="ScriptSEPO(<? echo $parent_id; ?>)">
</FORM>
</TD>
<?
/*
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="view thread">
<INPUT TYPE="hidden" NAME="perform" VALUE="InterfaceView(ScriptShow)">
</FORM>
</TD>
<?
		$result=mysql_query("SELECT ID,RuleID FROM RuleNext ".
			"WHERE NextID=".$rule_id)
			or trigger_error("unable to select",STOP);
		if(mysql_num_rows($result)>0)
		{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_rule">
<OPTION SELECTED VALUE="0">parent rule list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=".$row[1].">".RuleName($row[1])." (".
			RuleType($row[1]).")</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="view">
<INPUT TYPE="hidden" NAME="process" VALUE="RuleSubView">
</FORM>
</TD>
<?
		}
*/
?>
</TR>
</TABLE>
<TABLE>
<TR>
<?
		$result=mysql_query("SELECT ID,Pattern FROM RuleEntry ".
			"WHERE RuleID=".$rule_id)
			or trigger_error("unable to select",STOP);
		if(mysql_num_rows($result)>0)
		{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_entry">
<OPTION SELECTED VALUE="0">entry point list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=".$row[0].">".$row[1]."</OPTION>";
	}
?>
</SELECT>
<?
	if(AuthorEnsureQuiet())
	{
?>
<INPUT TYPE="submit" NAME="task" VALUE="edit">
<INPUT TYPE="submit" NAME="task" VALUE="delete">
<INPUT TYPE="hidden" NAME="process" VALUE="EntrySubEdit">
<?
	}
?>
</FORM>
</TD>
<?
	}
?>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="add new entry point">
<INPUT TYPE="hidden" NAME="perform" VALUE="EntryAdd(<? echo $rule_id; ?>)">
</FORM>
</TD>
<?
	}
?>
</TR>
</TABLE>
<TABLE>
<TR>
<?
		$result=mysql_query("SELECT ID,Response FROM RuleResponse ".
			"WHERE RuleID=".$rule_id)
			or trigger_error("unable to select",STOP);
		if(mysql_num_rows($result)>0)
		{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_response">
<OPTION SELECTED VALUE="0">response list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		if(strlen($row[1])>80)
			$row[1]=substr($row[1],0,77)."...";
		echo "<OPTION VALUE=".$row[0].">".$row[1]."</OPTION>";
	}
?>
</SELECT>
<?
	if(AuthorEnsureQuiet())
	{
?>
<INPUT TYPE="submit" NAME="task" VALUE="edit">
<INPUT TYPE="submit" NAME="task" VALUE="delete">
<INPUT TYPE="hidden" NAME="process" VALUE="ResponseSubEdit">
<?
	}
?>
</FORM>
</TD>
<?
		}
?>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="add new response">
<INPUT TYPE="hidden" NAME="perform" VALUE="ResponseAdd(<? echo $rule_id; ?>)">
</FORM>
</TD>
<?
		}
?>
</TR>
</TABLE>
<TABLE>
<TR>
<?
		$result=mysql_query("SELECT ID,NextID FROM RuleNext ".
			"WHERE RuleID=".$rule_id)
			or trigger_error("unable to select",STOP);
		if(mysql_num_rows($result)>0)
		{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_next">
<OPTION SELECTED VALUE="0">expectation list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=".$row[1].">".RuleName($row[1])." (".
			RuleType($row[1]).")</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" NAME="task" VALUE="view">
<?
	if(AuthorEnsureQuiet())
	{
?>
<INPUT TYPE="submit" NAME="task" VALUE="delete">
<?
	}
?>
<INPUT TYPE="hidden" NAME="process" VALUE="NextSubView(<? echo $rule_id; ?>">
</FORM>
</TD>
<?
		}
?>
<?
		$result=mysql_query("SELECT ID FROM Rule ".
			"WHERE ParentID=".$parent_id)
			or trigger_error("unable to select",STOP);
		while($row=mysql_fetch_row($result))
			$rule1[]=$row[0];
		$result=mysql_query("SELECT NextID FROM RuleNext ".
			"WHERE RuleID=".$rule_id)
			or trigger_error("unable to select",STOP);
		while($row=mysql_fetch_row($result))
			$rule2[]=$row[0];
		if(sizeof($rule2)>0) $rule=array_diff($rule1,$rule2);
		else $rule=$rule1;
		if(sizeof($rule)>0)
		{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="next_add">
<OPTION SELECTED VALUE="0">rule list</OPTION>
<?
	reset($rule);
	while($elem=current($rule))
	{
		echo "<OPTION VALUE=".$elem.">".RuleName($elem)." (".
			RuleType($elem).")</OPTION>";
		next($rule);
	}
?>
</SELECT>
<INPUT TYPE="submit" NAME="task" VALUE="view">
<?
	if(AuthorEnsureQuiet())
	{
?>
<INPUT TYPE="submit" NAME="task" VALUE="link">
<?
	}
?>
<INPUT TYPE="hidden" NAME="perform" VALUE="NextAdd(<? echo $rule_id; ?>)">
</FORM>
</TD>
<?
		}
?>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="link new rule">
<INPUT TYPE="hidden" NAME="next_add" VALUE="<? echo $rule_id; ?>">
<INPUT TYPE="hidden" NAME="perform" VALUE="NextAddRule(<? echo $parent_id; ?>)">
</FORM>
</TD>
<?
	}
?>
</TR>
</TABLE>
<?
		ScriptVisualise($parent_id);
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleRespond($id)
	{
		$result=mysql_query("SELECT Response FROM RuleResponse WHERE ".
			"RuleID=".$id)
			or trigger_error("unable to select",STOP);
		$num=mysql_num_rows($result);
		if($num==0) return "Botson remains quiet.";
		mysql_data_seek($result,rand(0,$num-1));
		$row=mysql_fetch_row($result);
		return "Botson says, \"".$row[0]."\"";
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleSelectNext($id)
	{
		$result=mysql_query("SELECT Pattern FROM RuleEntry WHERE RuleID=".$id)
			or trigger_error("unable to select");
		if(mysql_num_rows($result)==0) return false;
		while($row=mysql_fetch_row($result))
		{
			echo "<OPTION VALUE=".$id.">".$row[0]."</OPTION>";
		}
		return true;
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleFinish($id)
	{
		if((!isset($id))||($id==0)) return false;
		$result=mysql_query("SELECT Type FROM Rule WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
		if($row[0]=="Fail")
		{
			echo "<P>Botson has failed to reach the goal of the thread.</P>";
			return true;
		}
		if($row[0]=="Goal")
		{
			echo "<P>Botson has reached the goal of the thread!</P>";
			return true;
		}
		return false;
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleName($id)
	{
		$result=mysql_query("SELECT Label FROM Rule WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if(!$row) return "Nameless rule";
		return $row[0];
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleType($id)
	{
		$result=mysql_query("SELECT Type FROM Rule WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if(!$row) return "Typeless rule";
		return $row[0];
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleSubView()
	{
		global $rule_id;
		global $sub_rule;
		$rule_id=$sub_rule;
		InterfaceView("RuleShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleEdit($id)
	{
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Rule","WHERE ID=".$id)) return;
		$result=mysql_query("SELECT Label,Description,Type ".
			"FROM Rule WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
?>
<H2>update rule information</H2>
<P>Please edit the information shown in the form below and click on
"update rule information" to execute the changes.</P>
<FORM METHOD="Post" ACTION="Index.html">
Rule's Purpose:
<SELECT NAME="update_rule[type]">
<OPTION <? if($row[2]=='Entry') { ?>SELECTED <? } ?>
VALUE='Entry'>the entry point of the thread</OPTION>
<OPTION <? if($row[2]=='Body') { ?>SELECTED <? } ?>
VALUE='Body'>part of the body of the thread</OPTION>
<OPTION <? if($row[2]=='Fail') { ?>SELECTED <? } ?>
VALUE='Fail'>indicate that the thread has failed</OPTION>
<OPTION <? if($row[2]=='Goal') { ?>SELECTED <? } ?>
VALUE='Goal'>indicate that the goal has been satisfied</OPTION>
</SELECT><BR>
Rule's Label:
<INPUT SIZE="32" NAME="update_rule[label]" VALUE="<? echo $row[0]; ?>"><BR>
Rule's Description:<BR>
<TEXTAREA NAME="update_rule[description]" ROWS=10 COLS=60>
<? echo $row[1]; ?></TEXTAREA><BR>
<INPUT TYPE="submit" VALUE="update rule information">
<INPUT TYPE="hidden" NAME="update_rule[id]" VALUE=<? echo $id; ?>>
<INPUT TYPE="hidden" NAME="perform" VALUE="RulePerformUpdate">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function RulePerformUpdate()
	{
		global $author_id;
		global $update_rule;
		global $rule_id;
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Rule","WHERE ID=".$update_rule["id"])) return;
		$result=mysql_query("UPDATE Rule SET Label=\"".$update_rule["label"].
			"\",Description=\"".$update_rule["description"]."\",Type='".
			$update_rule["type"]."',ChangeID=".
			$author_id." WHERE ID=".$update_rule["id"])
			or trigger_error("unable to update rule",STOP);
		DatabaseUnlock("Rule","WHERE ID=".$update_rule["id"]);
		trigger_error("the rule named \"".$update_rule["label"]."\" has ".
			"been updated",GOOD);
		$rule_id=$update_rule["id"]; // view once edit over
		InterfaceView("RuleShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function RuleAdd($id)
	{
		global $author_id;
		global $action;
		if(!AuthorEnsure()) return;
		$result=mysql_query("INSERT INTO Rule (Label,Description,ParentID,".
			"Type,CreateID,Created) VALUES (\"New Rule\",\"New rule\",".$id.",".
			"'Body',".$author_id.",NOW())")
			or trigger_error("unable to create new rule",STOP);
		$child=mysql_insert_id();
		$action="RuleEdit(".$child.")";
		return $child;
	}
/*============================================================================*/
?>
