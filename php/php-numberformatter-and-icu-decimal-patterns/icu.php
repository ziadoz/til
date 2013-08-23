<?php
// ICU Decimal Patterns: 
// http://www.icu-project.org/apiref/icu4c/classDecimalFormat.html#details

$price = 1560.52;
$formatter = new NumberFormatter('en_CA', NumberFormatter::CURRENCY);

// Default ICU Decimal Pattern:
echo $formatter->getPattern();    // ¤#,##0.00;(¤#,##0.00)
echo $formatter->format($price);  // $1,560.52
  
// Custom ICU Decimal Pattern: 
$formatter->setPattern('¤#,##0.00 ¤¤;(¤#,##0.00 ¤¤)');
echo $formatter->format($price); // $1,560.52 CAD