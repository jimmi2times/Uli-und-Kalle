<?php
/*
 * Created on 07.04.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once('../../wp-config.php' );
require_once(ABSPATH.'ulinew/_mainlibs/includes/xajax/xajax_core/xajax.inc.php');
global $xajax;
$xajax = new xajax();
$xajax->setFlag("debug",true);
$xajax->registerFunction("myFunction");

$xajax->processRequest();

 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?
$sJsURI = 'http://localhost/liga_2009/ulinew/_mainlibs/includes/xajax/';
$xajax->printJavascript($sJsURI); 
?>
</head>
<body>
<div id="SomeElementId"></div>
<button onclick="xajax_myFunction('It worked!');"></pre>



</body>
</html>

<?
function myFunction($arg)
{
    // do some stuff based on $arg like query data from a database and
    // put it into a variable like $newContent
        $newContent = "Value of $arg: ".$arg;
    
    // Instantiate the xajaxResponse object
    $objResponse = new xajaxResponse();
    
    // add a command to the response to assign the innerHTML attribute of
    // the element with id="SomeElementId" to whatever the new content is
    $objResponse->assign("SomeElementId","innerHTML", $newContent);
    
    //return the  xajaxResponse object
    return $objResponse;
}



?>