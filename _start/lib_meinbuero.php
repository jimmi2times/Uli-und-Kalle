<?php
/*
 * Created on 30.03.2009
 *
 * Die Funktionen für die Optionsdatei
 */
 

/**
 * gibt das formular für die basiseinstellungen aus
 * 31.03.09
 * 
 */
function print_uli_mainoptions(){
global $option, $uli;
$html  = "\n";
$html .= uli_start_form('ulioptions', '?action=updateuli');
$html .= uli_input('text', 'uliname', $uli['uliname'], 'ulioptions', 30, 50, 'readonly = "readonly"');
$html .= uli_textarea('ulitext', $uli['ulitext'], 'ulioptions', 30, 10);
$html .= uli_end_form(SUBMIT, 'ulioptions');
return $html;
}


/**
 * gibt den Bereich für die Wahl der Farben und der Wappen aus
 * 07.04.09
 * TODO Styles bearbeiten
 */
function print_uli_colors(){
global $option, $uli;
$html  = "\n";
/* Wappen - File Upload */
$html .= uli_start_form('uliwappen', '?action=updatewappen');
$html .= uli_input('file', 'uliwappen', '', 'ulioptions');
$html .= uli_end_form(SUBMIT, 'ulioptions');

/* Farben: Ajax-Onclick-Change */
/*
 * red: FF0000
 * yellow: FFFF00
 * black: 000000
 * white: ffffff
 * blue: 0000FF
 * green: 018f51
 * brown: A52A2A
 */
$html .= '<div id = "ulicolors1" class="ulicolors color1">';
$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'white', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#ffffff\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'yellow', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#FFFF00\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'red', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#FF0000\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'blue', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#0000FF\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'green', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#018f51\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'brown', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#A52A2A\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color1', 'black', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#000000\', \'tcolor1\');"').'</div>';$html .= "\n";
	$html .= "\n";
	$html .= '  <div class="one_ulicolor white"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor yellow"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor red"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor blue"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor green"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor brown"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor black"></div>';$html .= "\n";
$html .= '</div>';
$html .= "\n";


$html .= '<div id = "ulicolors2" class="ulicolors color2">';
$html .= "\n";

	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'white', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#ffffff\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'yellow', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#FFFF00\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'red', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#FF0000\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'blue', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#0000FF\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'green', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#018f51\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'brown', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#A52A2A\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor">'.uli_input('radio', 'color2', 'black', 'ulioptions', '', $attributes, 'onclick = "xajax_change_color('.$option['uliID'].',\'#000000\', \'tcolor2\');"').'</div>';$html .= "\n";
	$html .= "\n";
	$html .= '  <div class="one_ulicolor white"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor yellow"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor red"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor blue"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor green"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor brown"></div>';$html .= "\n";
	$html .= '  <div class="one_ulicolor black"></div>';$html .= "\n";
$html .= '</div>';
$html .= "\n";
return $html;	
}


/**
 * durch xajax initialisierte Funktion, die Farbwerte speichert
 * Vielleicht sollte man eine allgemeine Xajax Update Funktion schreiben
 * ändert die Hintergrundfarbe des Containers
 * 07.04.09
 */
function change_color($uliID, $color, $whichcolor){
$objResponse = new xajaxResponse();
if ($whichcolor == 'tcolor1'){$container = 'ulicolors1';}
if ($whichcolor == 'tcolor2'){$container = 'ulicolors2';}
$cond[] = array("col" => "ID", "value" => $uliID);		
$value[] = array("col" => $whichcolor, "value" => $color);
uli_update_record('uli', $cond, $value);
$objResponse->assign($container,"style.background", $color);
return $objResponse;
}


/** Testfunktion XAJAX */
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


/*
print_uli_trikots();
print_uli_manager();
print_uli_location();
print_uli_styles();
*/

 
?>
