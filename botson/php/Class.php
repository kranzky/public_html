<?
/*============================================================================*/
	/*
	 *
	 */
	function ClassName($id)
	{
		$result=mysql_query("SELECT Label FROM Class WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result);
		if(!$row) return "Nameless node";
		return $row[0];
	}
/*============================================================================*/
	/*
	 *
	 */
	function ClassShow()
	{
		global $class_id;
		do
		{
			$result=mysql_query("SELECT ParentID,Label,Description FROM Class ".
				"WHERE ID=".$class_id)
				or trigger_error("select failed",STOP);
			$row=mysql_fetch_row($result);
			if(!$row)
			{
				if($class_id==1) trigger_error("class hierarchy invalid",STOP);
				$class_id=1;
			}
		}
		while(!$row);
		$nodename=$row[1];
?>
<H2>node view: <? echo $nodename; ?></H2>
<P><? echo $row[2]; ?></P>
<TABLE>
<TR>
<?
	if(AuthorEnsureQuiet())
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="edit this node">
<INPUT TYPE="hidden" NAME="action" VALUE="ClassEdit(<? echo $class_id; ?>)">
</FORM>
</TD>
<?
	}
?>
<?
/*
?>
<?
	if($row[0]!=0)
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<INPUT TYPE="submit" VALUE="view parent node">
<INPUT TYPE="hidden" NAME="perform" VALUE="ClassParent(<? echo $row[0]; ?>)">
</FORM>
</TD>
<?
	}
?>
<?
*/
?>
</TR>
</TABLE>
<TABLE>
<TR>
<?
/*
?>
<?
	$result=mysql_query("SELECT Label,ID FROM Class WHERE ParentID=".$class_id)
		or trigger_error("unable to select",STOP);
	if(mysql_num_rows($result)>0)
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_class">
<OPTION SELECTED VALUE="0">node list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=".$row[1].">".$row[0]."</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="view node">
<INPUT TYPE="hidden" NAME="process" VALUE="ClassSubView">
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
<INPUT TYPE="submit" VALUE="add new node">
<INPUT TYPE="hidden" NAME="perform" VALUE="ClassAdd(<? echo $class_id; ?>)">
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
/*
?>
<?
	$result=mysql_query("SELECT ID FROM Script WHERE ParentID=".$class_id)
		or trigger_error("unable to select",STOP);
	if(mysql_num_rows($result)>0)
	{
?>
<TD>
<FORM METHOD="Post" ACTION="Index.html">
<SELECT NAME="sub_script">
<OPTION SELECTED VALUE="0">thread list</OPTION>
<?
	while($row=mysql_fetch_row($result))
	{
		echo "<OPTION VALUE=".$row[0].">".ScriptName($row[0])."</OPTION>";
	}
?>
</SELECT>
<INPUT TYPE="submit" VALUE="view thread">
<INPUT TYPE="hidden" NAME="process" VALUE="ScriptSubView">
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
<INPUT TYPE="submit" VALUE="add new thread">
<INPUT TYPE="hidden" NAME="perform" VALUE="ScriptAdd(<? echo $class_id; ?>)">
</FORM>
</TD>
<?
	}
?>
</TR>
</TABLE>
<?
		ClassVisualise($class_id);
	}
/*============================================================================*/
	/*
	 *
	 */
	function ClassSubView()
	{
		global $class_id;
		global $sub_class;
		$class_id=$sub_class;
		InterfaceView("ClassShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function ClassEdit($id)
	{
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Class","WHERE ID=".$id)) return;
		$result=mysql_query("SELECT Label,Description FROM Class WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
?>
<H2>update node information</H2>
<P>Please edit the information shown in the form below and click on
"update node information" to execute the changes.</P>
<FORM METHOD="Post" ACTION="Index.html">
Node's Label:
<INPUT SIZE="32" NAME="update_class[label]" VALUE="<? echo $row[0]; ?>"><BR>
Node's Description:<BR>
<TEXTAREA NAME="update_class[description]" ROWS=10 COLS=60>
<? echo $row[1]; ?></TEXTAREA><BR>
<INPUT TYPE="submit" VALUE="update node information">
<INPUT TYPE="hidden" NAME="update_class[id]" VALUE=<? echo $id; ?>>
<INPUT TYPE="hidden" NAME="perform" VALUE="ClassPerformUpdate">
</FORM>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ClassPerformUpdate()
	{
		global $author_id;
		global $update_class;
		global $class_id;
		if(!AuthorEnsure()) return;
		if(!DatabaseLock("Class","WHERE ID=".$update_class["id"])) return;
		$result=mysql_query("UPDATE Class SET Label=\"".$update_class["label"].
			"\",Description=\"".$update_class["description"]."\",ChangeID=".
			$author_id." WHERE ID=".$update_class["id"])
			or trigger_error("unable to update node",STOP);
		DatabaseUnlock("Class","WHERE ID=".$update_class["id"]);
		trigger_error("the node named \"".$update_class["label"]."\" has ".
			"been updated",GOOD);
		$class_id=$update_class["id"]; // view once edit over
		InterfaceView("ClassShow");
	}
/*============================================================================*/
	/*
	 *
	 */
	function ClassAdd($id)
	{
		global $author_id;
		global $action;
		if(!AuthorEnsure()) return;
		$result=mysql_query("INSERT INTO Class (Label,Description,ParentID,".
			"CreateID,Created) VALUES (\"New Node\",\"New node\",".$id.",".
			$author_id.",NOW())")
			or trigger_error("unable to create new node",STOP);
		$child=mysql_insert_id();
		$action="ClassEdit(".$child.")";
	}
/*============================================================================*/
	/*
	 *
	 */
	function ClassParent($id)
	{
		global $class_id;
		$class_id=$id;
	}
/*============================================================================*/
	/*
	 *
	 */
	function ClassVisualise($id)
	{
		$file=fopen("diagram/node_".$id.".dot","w")
			or trigger_error("unable to open file",STOP);
		fwrite($file,"digraph node_".$id." {\n");
		/*
		$result=mysql_query("SELECT ParentID FROM Class WHERE ID=".$id)
			or trigger_error("unable to select",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("no result",STOP);
		if($row[0]!=0)
		{
			fwrite($file,"enter [label=\"".ClassName($row[0])."\",URL=\"Index.html?perform=ClassSubView&sub_class=".$row[0]."\",shape=diamond];\n");
			fwrite($file,"enter -> node_".$id.";\n");
		}
		*/
		ClassRecurse($file,1,$id);
		fwrite($file,"}\n");
		fclose($file);
		system("/home/hutch/bin/dot -Tgif diagram/node_".$id.".dot -o diagram/node_".$id.".gif");
		system("/home/hutch/bin/dot -Timap diagram/node_".$id.".dot -o diagram/node_".$id.".map");
?>
<TABLE ID="Diagram"><TR><TD>
<A HREF="diagram/node_<? echo $id; ?>.map">
<IMG SRC="diagram/node_<? echo $id; ?>.gif" ISMAP BORDER=0>
</A>
</TD></TR></TABLE>
<?
	}
/*----------------------------------------------------------------------------*/
	/*
	 *
	 */
	function ClassRecurse($file,$id,$base)
	{
		if($id==$base)
		{
			$chosen=",color=yellow,style=filled";
		} else {
			$chosen="";
		}
		fwrite($file,"node_".$id." [label=\"".AddSlashes(ClassName($id))."\",URL=\"Index.html?perform=ClassSubView&sub_class=".$id."\"".$chosen."];\n");
		$result=mysql_query("SELECT ID FROM Class WHERE ParentID=".$id)
			or trigger_error("unable to select",STOP);
		while($row=mysql_fetch_row($result))
		{
			fwrite($file,"node_".$id." -> node_".$row[0].";\n");	
			ClassRecurse($file,$row[0],$base);
		}
		$result=mysql_query("SELECT ID FROM Script WHERE ParentID=".$id)
			or trigger_error("unable to select",STOP);
		while($row=mysql_fetch_row($result))
		{
			fwrite($file,"thread_".$row[0]." [label=\"".AddSlashes(ScriptName($row[0]))."\",shape=box,URL=\"Index.html?perform=ScriptSubView&sub_script=".$row[0]."\"];\n");
			fwrite($file,"node_".$id." -> thread_".$row[0].";\n");	
		}
	}
/*============================================================================*/
?>
