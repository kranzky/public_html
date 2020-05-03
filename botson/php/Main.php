<? session_start(); ?>
<!-- the botson authoring tool is (c) 2001 artificial intelligence nv -->
<DOCTYPE HTML PUBLIC "-//Netscape Comm. Corp.//DTD HTML//EN">
<HTML>
<HEAD>
<TITLE>botson authoring tool</TITLE>
<LINK REL="StyleSheet" HREF="style/ai.css" TYPE="text/css" MEDIA="screen">
<LINK REV="Made" HREF="mailto:hutch@tmbg.org">
</HEAD>
<BODY>
<H1>botson authoring tool</H1>
<HR>
<?
	DatabaseConnect();
	InterfaceExecute($process,false);
	SessionRecover();
	AuthorRetrieve();
	InterfaceExecute($perform,false);
	InterfaceExecute($action);
	InterfaceBody();
	InterfaceExecute($view);
	InterfaceMain();
?>
<HR>
<P ID="Copyright">the botson authoring tool is copyright (c) 2001
artificial intelligence enterprises<BR>it was written by jason hutchens
using a combination of html, php, mysql and graphviz</P>
</BODY>
</HTML>
<!-- the botson authoring tool is (c) 2001 artificial intelligence nv -->
