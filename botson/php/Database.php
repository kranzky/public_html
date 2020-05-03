<?
/*============================================================================*/
	/*
	 *		Open a persistent connection to the MySQL database server and
	 *		select the BotsonTool database.
	 */
	function DatabaseConnect()
	{
		$mysql=mysql_pconnect("localhost","hutch","wally37")
			or trigger_error("i was unable to connect to mysql",STOP);
		mysql_select_db("BotsonTool",$mysql)
			or trigger_error("i was unable to select the database",STOP);
	}
/*============================================================================*/
	/*
	 *		Lock the row of the specified table which satisfy the where clause.
	 */
	function DatabaseLock($table,$where)
	{
		global $author_id;
		$result=mysql_query("SELECT LockedID FROM ".$table." ".$where)
			or trigger_error("unable to select from table",STOP);
		assert(mysql_num_rows($results)<=1)
			or trigger_error("tried to lock more than one row",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
		if($row[0]!=0)
		{
			if($row[0]==$author_id) return true;
			trigger_error(AuthorName($row[0])." is already doing that",EVIL);
			return false;
		}
		$result=mysql_query("UPDATE ".$table." SET LockedID=".$author_id." ".
			$where) or trigger_error("unable to update table",STOP);
		return true;
	}
/*----------------------------------------------------------------------------*/
	/*
	 *		Unlock a row of the table.
	 */
	function DatabaseUnlock($table,$where)
	{
		global $author_id;
		$result=mysql_query("SELECT LockedID FROM ".$table." ".$where)
			or trigger_error("unable to select from table",STOP);
		assert(mysql_num_rows($results)<=1)
			or trigger_error("tried to unlock more than one row",STOP);
		$row=mysql_fetch_row($result)
			or trigger_error("nothing selected",STOP);
		if($row[0]!=$author_id)
		{
			if($row[0]==0) return true;
			trigger_error(AuthorName($row[0])." is already doing that",EVIL);
			return false;
		}
		$result=mysql_query("UPDATE ".$table." SET LockedID=0 ".$where)
			or trigger_error("unable to update table",STOP);
		return true;
	}
/*============================================================================*/
?>
