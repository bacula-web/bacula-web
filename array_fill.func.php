<?php

// For PHP version < 4.2.0 missing the array_fill function,
// I provide here an alternative. -Philippe

function array_fill($iStart, $iLen, $vValue) {
	$aResult = array();
    for ($iCount = $iStart; $iCount < $iLen + $iStart; $iCount++) {
    	$aResult[$iCount] = $vValue;
    }
    return $aResult;
}
                       
?>