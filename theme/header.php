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



global $option, $page, $uli; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title>Das Uli und Kalle Chefauskennerb&uuml;ro - <?php echo $page['name']; ?></title>



<!-- jquery -->
<link type="text/css" href="<?PHP echo $option['uliroot']?>/theme/jquery/css/smoothness/jquery-ui-1.8.13.custom.css" rel="Stylesheet" />



<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/theme/jquery/js/jquery-ui-1.8.13.custom.min.js"></script>
<script type="text/javascript" src="<?PHP echo $option['uliroot']?>/theme/jquery/autoNumeric-master/autoNumeric-min.js"></script>




<!--  uli und kalle stylesheet -->
<link rel="stylesheet" type="text/css" href="<?PHP echo $option['uliroot'];?>/theme/format.css" />

<!-- dynamische stylesheets -->
<!--  die koennten eigentlich auch ueber jquery zugewiesen werden? -->
<style type="text/css">
#page {background: url("<?php echo $option['uliroot'].'/theme/graphics/bg/'.$page['sub'].'.jpg';?>");} 
</style>

<script>
$('a.playerinfo, .playerinfo').live('click', function() {
	var playerID = this.id;
	if (playerID > 0) {
		$.ajax({
			type: "POST", url: "<?php echo $option['uliroot']; ?>/_mainlibs/ajax_global.php", data: "action=printplayerinfo&playerID="+playerID,
			complete: function(data){
				$("#playerinfo").html(data.responseText);
			}
		 });
		$("#playerinfo").dialog();
		}
	else {
		return false;
		}
	});
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-5174521-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>


<body class="color1">
<div id="page">
<?php print_header_menu($page);?>




<div id="content">
<div id="playerinfo" title="Spielerinfo" style="display:none;"></div>
	