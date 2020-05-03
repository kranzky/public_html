<?
/*============================================================================*/
	/*
	 *
	 */
	function ScriptShow()
	{
		global $script_id;
		global $rule_id;
		$rule_id=0;
		$result=mysql_query("SELECT Label,Description,ParentID FROM Script ".
			"WHERE ID=".$script_id)
			or trigger_error("select failed",STOP);
		$row=mysql_fetch_row($result);
		if(!$row)
		{
			trigger_error("no such thread exists",EVIL);
			InterfaceView("ClassShow");
			ClassShow();
			return;
		}
		$classname=$row[0];
?>
<H2>thread view: <? echo $classname; ?></H2>
<P><? echo $row[1]; ?></P>
<TABLE>
<TR>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="run thread">
<INPUT TYPE="hidden" NAME="action" VALUE="ScriptRun(<? echo $script_id; ?>)">
</FORM>
</TD>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="edit thread">
<INPUT TYPE="hidden" NAME="action" VALUE="ScriptEdit(<? echo $script_id; ?>)">
</FORM>
</TD>
<?
	}
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="export in SEPO format">
<INPUT TYPE="hidden" NAME="action" VALUE="ScriptSEPO(<? echo $script_id; ?>)">
</FORM>
</TD>
<?
/*
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="view parent node">
<INPUT TYPE="hidden" NAME="perform" VALUE="InterfaceView(ClassShow)">
</FORM>
<?
*/
?>
</TD>
</TR>
</TABLE>
<TABLE>
<TR>
<?
/*
?>
<?
	$result=mysql_query("SELECT Label,ID,Type FROM Rule WHERE ParentID=".
		$script_id)
		or trigger_error("unable to select",STOP);
	if(mysql_num_rows($result)>0)
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_rule">
<OPTION SELECTED VALUE="0">rule list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=".$row[1].">".$row[0]." (".$row[2].")</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="view rule">
<INPUT TYPE="hidden" NAME="process" VALUE="RuleSubView">
</FORM>
</TD>
<?
	}
?>
<?
*/
?>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="add new rule">
<INPUT TYPE="hidden" NAME="perform" VALUE="RuleAdd(<? echo $script_id; ?>)">
</FORM>
</TD>
<?
	}
?>
</TR>
</TABLE>
<?
		ScriptVisualise($script_id);
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptName($id)
	{
		$result=mysql_query("SELECT Label FROM Script WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if(!$row) return "Nameless thread";
		return $row[0];
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptSubView()
	{
		global $script_id;
		global $sub_script;
		$script_id=$sub_script;
		InterfaceView("ScriptShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptEdit($id)
	{
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Script","WHERE ID=".$id)) return;
		$result=mysql_query("SELECT Label,Description FROM Script WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
?>
<H2>update thread information</H2>
<P>Please edit the information shown in the form below and click on
"update thread information" to execute the changes.</P>
<FORM METHOD="Post" ACTION="Index.html">
Thread's Label:
<INPUT SIZE="32" NAME="update_script[label]" VALUE="<? echo $row[0]; ?>"><BR>
Thread's Description:<BR>
<TEXTAREA NAME="update_script[description]" ROWS=10 COLS=60>
<? echo $row[1]; ?></TEXTAREA><BR>
<INPUT TYPE="submit" VALUE="update thread information">
<INPUT TYPE="hidden" NAME="update_script[id]" VALUE=<? echo $id; ?>>
<INPUT TYPE="hidden" NAME="perform" VALUE="ScriptPerformUpdate">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ScriptPerformUpdate()
	{
		global $author_id;
		global $update_script;
		global $script_id;
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Script","WHERE ID=".$update_script["id"])) return;
		$result=mysql_query("UPDATE Script SET Label=\"".$update_script["label"].
			"\",Description=\"".$update_script["description"]."\",ChangeID=".
			$author_id." WHERE ID=".$update_script["id"])
			or trigger_error("unable to update thread",STOP);
		DatabaseUnlock("Script","WHERE ID=".$update_script["id"]);
		trigger_error("the thread named \"".$update_script["label"]."\" has ".
			"been updated",GOOD);
		$script_id=$update_script["id"]; // view once edit over
		InterfaceView("ScriptShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptAdd($id)
	{
		global $author_id;
		global $action;
		if(!AuthorEnsure()) return;
		$result=mysql_query("INSERT INTO Script (Label,Description,ParentID,".
			"CreateID,Created) VALUES (\"New Thread\",\"New thread\",".$id.",".
			$author_id.",NOW())")
			or trigger_error("unable to create new thread",STOP);
		$child=mysql_insert_id();
		$action="ScriptEdit(".$child.")";
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptSEPO($id)
	{
?>
<H2>export thread in SEPO format not yet implemented</H2>
<P>In the future you will be able to save a thread in SEPO format, making
it easy to import new threads into Botson.
Right now, however, you get nothing.</P>
<?
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptRun($id)
	{
		global $rule_id;
		global $current_rule;
		if((!isset($current_rule))||($current_rule==0))
		{
			$result=mysql_query("SELECT ID FROM Rule WHERE ParentID=".$id." ".
				"AND Type='Entry'")
				or trigger_error("unable to select",STOP);
			if(mysql_num_rows($result)==0)
			{
				trigger_error("cannot run thread as no entry rule",EVIL);
				return;
			}
		}
		else
		{
			$result=mysql_query("SELECT NextID FROM RuleNext WHERE ".
				"RuleID=".$current_rule)
				or trigger_error("unable to select",STOP);
		}
?>
<H2>running thread named "<? echo ScriptName($id); ?>"</H2>
<?
		if((!isset($current_rule))||($current_rule==0))
		{
			echo "<P>You are entering the thread.</P>";
		} else {
			echo "<P>".RuleRespond($current_rule)."</P>";
		}
		if(RuleFinish($current_rule))
		{
			$rule_id=$current_rule;
			InterfaceView("RuleShow");
			return;
		}
		if(mysql_num_rows($result)==0)
		{
			trigger_error("cannot continue thread as no expectation",EVIL);
			$rule_id=$current_rule;
			InterfaceView("RuleShow");
			return;
		}
?>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="current_rule">
<OPTION SELECTED VALUE="0">select a reply</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		if(!RuleSelectNext($row[0])) $default[]=$row[0];
	}
	if(sizeof($default)>0)
	{
		echo "<OPTION VALUE=".$default[rand(0,sizeof($default)-1)].
			">(default)</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="reply to botson">
<INPUT TYPE="hidden" NAME="action" VALUE="ScriptRun(<? echo $id; ?>)">
</FORM>
<?
		if((isset($current_rule))&&($current_rule!=0))
		{
			$rule_id=$current_rule;
			InterfaceView("RuleShow");
		}
	}
/*============================================================================*/
	/*
	 *
	 */
	function ScriptVisualise($id)
	{
		global $rule_id;
		if((isset($rule_id))&&($rule_id!=0))
		{
			$file=fopen("diagram/thread_".$id."_".$rule_id.".dot","w")
				or trigger_error("unable to open file",STOP);
		} else {
			$file=fopen("diagram/thread_".$id.".dot","w")
				or trigger_error("unable to open file",STOP);
		}
		fwrite($file,"digraph thread_".$id." {\n");
		$result=mysql_query("SELECT ParentID FROM Script WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
		fwrite($file,"thread [label=\"".AddSlashes(ClassName($row[0]))."\",URL=\"Index.html?perform=ClassSubView&sub_class=".$row[0]."\",shape=diamond];\n");
		fwrite($file,"thread -> enter;\n");
		fwrite($file,"enter [label=\"".AddSlashes(ScriptName($id))."\",URL=\"Index.html?perform=ScriptSubView&sub_script=".$id."\",shape=box];\n");
		$result=mysql_query("SELECT ID,Label,Type FROM Rule WHERE ParentID=".$id)
			or trigger_error("unable to select",STOP);
		while($row=mysql_fetch_row($result))
		{
			switch($row[2])
			{
				case "Body": $colour="black"; break;
				case "Fail": $colour="red"; break;
				case "Goal": $colour="green"; break;
				case "Entry": $colour="blue"; break;
				default: break;
			}
			$result3=mysql_query("SELECT Response FROM RuleResponse WHERE ".
				"RuleID=".$row[0])
				or trigger_error("unable to select",STOP);
			$row3=mysql_fetch_row($result3);
			if(!$row3) $label="(silence)";
			else $label=$row3[0];
			if(strlen($label)>40)
				$label=substr($label,0,37)."...";
			$choice="color=".$colour;
			if((isset($rule_id))&&($rule_id==$row[0]))
			{
				$choice="color=yellow,style=filled";
			}
			fwrite($file,"rule_".$row[0]."[label=\"".AddSlashes($label)."\",".$choice.",URL=\"Index.html?perform=RuleSubView&sub_rule=".$row[0]."\"];\n");
			if($row[2]=="Entry")
			{
				$result3=mysql_query("SELECT Pattern FROM RuleEntry WHERE ".
					"RuleID=".$row[0])
					or trigger_error("unable to select",STOP);
				$row3=mysql_fetch_row($result3);
				if(!$row3) $label="(default)";
				else $label=$row3[0];
				if(strlen($label)>40)
					$label=substr($label,0,37)."...";
				fwrite($file,"enter -> rule_".$row[0]." [label=\"".AddSlashes($label)."\"];\n");
			}
			$result2=mysql_query("SELECT NextID FROM RuleNext WHERE RuleID=".$row[0])
				or trigger_error("unable to select",STOP);
			while($row2=mysql_fetch_row($result2))
			{
				$result3=mysql_query("SELECT Pattern FROM RuleEntry WHERE ".
					"RuleID=".$row2[0])
					or trigger_error("unable to select",STOP);
				$row3=mysql_fetch_row($result3);
				if(!$row3) $label="(default)";
				else $label=$row3[0];
				fwrite($file,"rule_".$row[0]." -> rule_".$row2[0]." [label=\"".AddSlashes($label)."\"];\n");
			}
		}
		fwrite($file,"}\n");
		fclose($file);
		if((isset($rule_id))&&($rule_id!=0))
		{
			system("/home/hutch/bin/dot -Tgif diagram/thread_".$id."_".$rule_id.".dot -o diagram/thread_".$id."_".$rule_id.".gif");
			system("/home/hutch/bin/dot -Timap diagram/thread_".$id."_".$rule_id.".dot -o diagram/thread_".$id."_".$rule_id.".map");
?>
<TABLE ID="Diagram"><TR><TD>
<A HREF="diagram/thread_<? echo $id."_".$rule_id; ?>.map">
<IMG SRC="diagram/thread_<? echo $id."_".$rule_id; ?>.gif" ISMAP BORDER=0>
</A>
</TD></TR></TABLE>
<?
		} else {
			system("/home/hutch/bin/dot -Tgif diagram/thread_".$id.".dot -o diagram/thread_".$id.".gif");
			system("/home/hutch/bin/dot -Timap diagram/thread_".$id.".dot -o diagram/thread_".$id.".map");
?>
<TABLE ID="Diagram"><TR><TD>
<A HREF="diagram/thread_<? echo $id; ?>.map">
<IMG SRC="diagram/thread_<? echo $id; ?>.gif" ISMAP BORDER=0>
</A>
</TD></TR></TABLE>
<?
		}
	}
/*============================================================================*/
?>
