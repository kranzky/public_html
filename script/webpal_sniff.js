/** Copyright (C) 2000 Artificial Intelligence NV **/
/*
 *		The WebPal system needs to query the user's web browser, as
 *		unfortunately different browsers treat DHTML differently.
 */
var user_agent = navigator.userAgent.toLowerCase();
var major_version = parseInt(navigator.appVersion);
var minor_version = parseFloat(navigator.appVersion);
var browser_ie = (user_agent.indexOf("msie")!=-1);
var browser_ie5 = (browser_ie&&(major_version==4)&&(user_agent.indexOf("msie 5.0")!=-1));
var browser_ie5p5 = (browser_ie&&(major_version==4)&&(user_agent.indexOf("msie 5.5")!=-1));
/** Copyright (C) 2000 Artificial Intelligence NV **/
