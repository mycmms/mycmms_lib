<?php

///////////////////////////////////////////////////////////////
// PHPagination v1.0 by Dan Palka, the most powerful wizard. //
// http://www.phpagination.com                               //
///////////////////////////////////////////////////////////////

function PHPagination($pCurrent, $pEnd, $PHPagi_urlPre, $PHPagi_urlPost, $PHPagi_uMultiplier = 1, $PHPagi_doArrows = FALSE) {

/////////////////////////////////////////////////
// LAYOUT SETTINGS - CHANGE TO SUIT YOUR NEEDS //
/////////////////////////////////////////////////
    
    $PHPagi_htmlPre = '';           // Precedes pagination output. Default is '<div class="PHPagination">'.
    $PHPagi_htmlPost = '';                        // Suceeds pagination output. Default is '</div>'.
    $PHPagi_htmlDivider = '|';                          // Separates results. Default is '|'.
    $PHPagi_htmlOmission = '&hellip;';                  // Replaces omitted results. Default is '$hellip;'.
    $PHPagin_sPage1 = ' <strong>Page ';                 // Goes before current page. Default is ' <strong>Page '.
    $PHPagin_sPage2 = ' of ';                           // Goes between current page and total pages. Default is ' of '.
    $PHPagin_s_Page3 = '</strong> ';                    // Goes after total pages. Default is '</strong> '.
    $PHPagi_leftArrow = '&laquo;';                     // The left arrow. Default is '&laquo;'.
    $PHPagi_rightArrow = '&raquo;';                    // The right arrow. Default is '&raquo;'.
    
//////////////////////////////////////////
// DON'T CHANGE ANYTHING BELOW THIS BOX //
//////////////////////////////////////////
    
    // Begin the PHPagination output.
    $pOutput = $PHPagi_htmlPre;
    
    // Check $pCurrent.
    if(is_numeric($pCurrent) == FALSE || $pCurrent < -1) {
        $pOutput = $pOutput . 'PHPagination Error: Current page' . $PHPagi_htmlPost;
        return $pOutput;
    }
    
    // Check $pTotal.
    if(is_numeric($pEnd) == FALSE || $pEnd < 0) {
        $pOutput = $pOutput . 'PHPagination Error: End page' . $PHPagi_htmlPost;
        return $pOutput;
    }
    
    // Check if current page is outside the range of pages.
    if($pCurrent > $pEnd) {
        // Return a nicely formatted error
        $pOutput = $pOutput . 'PHPagination Error: Current page is greater than end page' . $PHPagi_htmlPost;
        return $pOutput;
    }
    
    $pCurrent = floor($pCurrent / $PHPagi_uMultiplier);
    $pEnd = floor($pEnd / $PHPagi_uMultiplier);
    
    // Create "Page x of x" string since it's the same no matter what.
    $pPageXofX = $PHPagin_sPage1 . number_format($pCurrent + 1) . $PHPagin_sPage2 . number_format($pEnd + 1) . $PHPagin_s_Page3;
    
    // Build reusable chunks of hyperlink.
    $pLink1 = ' <a href="' . $PHPagi_urlPre;
    $pLink2 = $PHPagi_urlPost . '" class="columntitle">';
    $pLink3 = '</a> ';
    
    // Do we need a left arrow?
    if($PHPagi_doArrows == TRUE && $pCurrent > 0) {
        $pOutput = $pOutput . $pLink1 . (($pCurrent - 1) * $PHPagi_uMultiplier) . $pLink2 . $PHPagi_leftArrow . $pLink3 . $PHPagi_htmlDivider;
    }
    
    // How many result units do we want?
    if($pEnd >= 14) {
        $pUnits = 14;
    } else {
        $pUnits = $pEnd;
    }
    
    // Which ellipses do we need?
    if (($pEnd - $pUnits) >= 2) {
        if ($pCurrent >= 8 && $pCurrent <= ($pEnd - 8)) {
            $firstEllipsis = TRUE;
            $secondEllipsis = TRUE;
        } elseif ($pCurrent >= 8 && $pCurrent >= ($pEnd - 7)) {
            $firstEllipsis = TRUE;
            $secondEllipsis = FALSE;
        } else {
            $firstEllipsis = FALSE;
            $secondEllipsis = TRUE;
        }
    } elseif (($pEnd - $pUnits) == 1) {
        if ($pCurrent >= 8) {
            $firstEllipsis = TRUE;
            $secondEllipsis = FALSE;
        } else {
            $firstEllipsis = FALSE;
            $secondEllipsis = TRUE;
        }
    } else {
        $firstEllipsis = FALSE;
        $secondEllipsis = FALSE;
    }
    
    // Where is PageXofX located?
    if ($firstEllipsis == TRUE && $secondEllipsis == TRUE) {
        $pageLocation = 7;
    } elseif ($firstEllipsis == TRUE) {
        $pageLocation = (14 - ($pEnd - $pCurrent));
    } elseif ($firstEllipsis == FALSE && $pCurrent >= 0) {
        $pageLocation = $pCurrent;
    } else {
        $pageLocation = -1;
    }
    
    // Loop through each result unit.
    for($i = 0; $i <= $pUnits; $i++) {
        if ($i == 3 && $firstEllipsis == TRUE) {
            $pOutput = $pOutput . $PHPagi_htmlOmission;
            $thisIsAnEllipsis = TRUE;
        } elseif ($i == 11 && $secondEllipsis == TRUE) {
            $pOutput = $pOutput . $PHPagi_htmlOmission;
            $thisIsAnEllipsis = TRUE;
        } else {
            $thisIsAnEllipsis = FALSE;
        }
        
        if ($thisIsAnEllipsis == FALSE) {
            if ($i > 0) {
                if ($firstEllipsis == TRUE && $i == 4) {
                } elseif ($secondEllipsis == TRUE && $i == 12) {
                } else {
                    $pOutput = $pOutput . $PHPagi_htmlDivider;
                }
            }
            if ($pageLocation == $i) {
                $pOutput = $pOutput . $pPageXofX;
            } elseif ($thisIsAnEllipsis == FALSE) {
                if ($i <= 2) {
                    $pOutput = $pOutput . $pLink1 . ($i * $PHPagi_uMultiplier) . $pLink2 . number_format($i + 1) . $pLink3;
                } elseif ($i >= 3 && $i <= 11) {
                    if ($firstEllipsis == TRUE && $secondEllipsis == TRUE) {
                        $pOutput = $pOutput . $pLink1 . (($pCurrent - (7 - $i)) * $PHPagi_uMultiplier) . $pLink2 . number_format(($pCurrent - (7 - $i)) + 1) . $pLink3;
                    } elseif ($firstEllipsis == TRUE && $secondEllipsis == FALSE) {
                        $pOutput = $pOutput . $pLink1 . (($pEnd - (14 - $i)) * $PHPagi_uMultiplier) . $pLink2 . number_format(($pEnd - (14 - $i)) + 1) . $pLink3;
                    } elseif ($firstEllipsis == FALSE && $secondEllipsis == TRUE) {
                        $pOutput = $pOutput . $pLink1 . ($i * $PHPagi_uMultiplier) . $pLink2 . number_format($i + 1) . $pLink3;
                    } else {
                        $pOutput = $pOutput . $pLink1 . ($i * $PHPagi_uMultiplier) . $pLink2 . number_format($i + 1) . $pLink3;
                    }
                } elseif ($i >= 12) {
                    $pOutput = $pOutput . $pLink1 . (($pEnd - (14 - $i)) * $PHPagi_uMultiplier) . $pLink2 . number_format(($pEnd - (14 - $i)) + 1) . $pLink3;
                }
            }
        }
    }
    
    // Do we need a right arrow?
    if($PHPagi_doArrows == TRUE && $pCurrent < $pEnd) {
        $pOutput = $pOutput . $PHPagi_htmlDivider . $pLink1 . (($pCurrent + 1) * $PHPagi_uMultiplier) . $pLink2 . $PHPagi_rightArrow . $pLink3;
    }
    
    // End the PHPagination output.
    $pOutput = $pOutput . $PHPagi_htmlPost;
    
    return $pOutput;
    
}
?>
