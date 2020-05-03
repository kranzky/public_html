<?php
/*============================================================================*/
	define(STOP,E_USER_ERROR);
	define(EVIL,E_USER_WARNING);
	define(GOOD,E_USER_NOTICE);
	error_reporting(2047);
	set_error_handler("ErrorHandler");
/*============================================================================*/
	/*
	 *		We define our own error handler which shanghai's user errors and
	 *		does its own stuff.  We actually use this to display informative
	 *		messages most of the time.
	 */
	function ErrorHandler($errno,$errstr,$errfile,$errline)
	{
		switch($errno)
		{
			case STOP:
?>
<H2>i have a headache</H2>
<P>I'm having trouble thinking about things right now.
I recommend that you contact jason, either by telephoning him on
+972-54-288-914 or by sending an email to
<A HREF="mailto:hutch@a-i.com">hutch@a-i.com</A>.  Tell him that botson
sent you, and that line
<? echo $errline; ?> of file "<? echo $errfile; ?>" reported that
"<? echo $errstr; ?>".  You might also want to show him this:
"<? echo mysql_error(); ?>".  I hope jason can help you.</P>
</BODY>
</HTML>
<!-- the botson authoring tool is (c) 2001 artificial intelligence nv -->
<?
				exit -1;
				break;
			case EVIL:
?>
<H3>i was unable to do something</H3>
<P>I apologise profusely, but I cannot satisfy your request as
<? echo $errstr; ?>.  I'm really very sorry.</P>
<?
				break;
			case GOOD:
?>
<H3>i would like to let you know something</H3>
<P>For your information I would like you to know that
<? echo $errstr; ;?>.  Thanks for your attention!</P>
<?
				break;
			default:
				break;
		}
	}
/*============================================================================*/
?>
