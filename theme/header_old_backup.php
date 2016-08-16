<?PHP 
/* Das ist die Headerdatei fuer jede Uli und Kalle Seite
 * 
 * 25.03.09
 */

/**

wir probieren mal, das umzubauen
basis sollte jquery und die jquery UI sein
und dann einige jquery plugins

wenn es nicht klappt, dann fuer die kabine noch das bewaehrte YUI script. 
das koennte ne zeitfrage sein
 
moeglichst auf xajax verzichten.
moeglichst mit json bei ajax requests probieren und relativ viele dinge schon ins markup schreiben

 */





global $option, $page, $uli, $xajax; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title>Das Uli und Kalle Chefauskennerb&uuml;ro - <?php echo $page['name']; ?></title>


<!-- XAJAX -->
<? /* Ansteuern der Xajax Funktionen */
$sJsURI = $option['uliroot'].'/_mainlibs/includes/xajax/';
if ($xajax){
	$xajax->printJavascript($sJsURI);
	}?>

<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/theme/yui.css" />
<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/theme/tabview-core.css" />
<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/theme/border_tabs.css" />

<!-- YUI -->
<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/_mainlibs/includes/yui/build/container/assets/container.css" />


<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/theme/format.css" />


<style type="text/css">
.color1 {background-color: <? echo $uli['tcolor1'];?>;} 
.color2 {background-color: <? echo $uli['tcolor2'];?>;}
#page {background: url("<?php echo $option['uliroot'].'/theme/graphics/bg/'.$page['sub'].'.jpg';?>");} 
</style>




<!--
<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/_mainlibs/includes/yui/build/fonts/fonts-min.css" />
-->



<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/dragdrop/dragdrop.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/container/container.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/yahoo/yahoo-min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/event/event-min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/dom/dom-min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/json/json-min.js"></script>

<!-- charts -->
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/element/element-beta-min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/charts/charts-experimental-min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/yui/build/tabview/tabview-min.js"></script>

<!-- jquery -->
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/_mainlibs/includes/jquery/jquery.js"></script>


<!-- Javascript für die Messages --> 
<script>
		YAHOO.namespace("example.container");
		function init() {
			// Message
			YAHOO.example.container.message = new YAHOO.widget.Panel("message", { width:"320px", visible:false, draggable:true, close:true});
			YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.message.show, YAHOO.example.container.message, true);
			YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.message.hide, YAHOO.example.container.message, true);
			// Spielerinfo
			YAHOO.example.container.PlayerInfo = new YAHOO.widget.Panel("PlayerInfo", { width:"320px", visible:false, draggable:true, close:true});
			YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.PlayerInfo.show, YAHOO.example.container.PlayerInfo, true);
			YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.PlayerInfo.hide, YAHOO.example.container.PlayerInfo, true);
		}
		YAHOO.util.Event.addListener(window, "load", init);
</script>

</head>
<body class="color1">
<div id="page">
<?php print_header_menu($page);?>

<div id="content">
	