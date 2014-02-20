<?php if ($S < 0) { $S = 0; }
$D = floor($S/86400);
$H = floor($S/3600);
$M = floor($S/60);
$h = $H - 24*$D;
$m = $M - 60*$H;
$s = $S - 60*$M;

$string0day = '';
$string0hour = '';
$string0minute = '';
$string0second = ' '.__('0 second', 'easy-timer');
$string1day = ' '.__('1 day', 'easy-timer');
$string1hour = ' '.__('1 hour', 'easy-timer');
$string1minute = ' '.__('1 minute', 'easy-timer');
$string1second = ' '.__('1 second', 'easy-timer');
$stringNdays = ' [N] '.__('days', 'easy-timer');
$stringNhours = ' [N] '.__('hours', 'easy-timer');
$stringNminutes = ' [N] '.__('minutes', 'easy-timer');
$stringNseconds = ' [N] '.__('seconds', 'easy-timer');

$stringD = $string0day;
$stringH = $string0hour;
$stringM = $string0minute;
$stringS = $string0second;
$stringh = $string0hour;
$stringm = $string0minute;
$strings = $string0second;

if ($D == 1) { $stringD = $string1day; } elseif ($D > 1) { $stringD = str_replace('[N]', $D, $stringNdays); }
if ($H == 1) { $stringH = $string1hour; } elseif ($H > 1) { $stringH = str_replace('[N]', $H, $stringNhours); }
if ($M == 1) { $stringM = $string1minute; } elseif ($M > 1) { $stringM = str_replace('[N]', $M, $stringNminutes); }
if ($S == 1) { $stringS = $string1second; } elseif ($S > 1) { $stringS = str_replace('[N]', $S, $stringNseconds); }
if ($h == 1) { $stringh = $string1hour; } elseif ($h > 1) { $stringh = str_replace('[N]', $h, $stringNhours); }
if ($m == 1) { $stringm = $string1minute; } elseif ($m > 1) { $stringm = str_replace('[N]', $m, $stringNminutes); }
if ($s == 1) { $strings = $string1second; } elseif ($s > 1) { $strings = str_replace('[N]', $s, $stringNseconds); }

if ($S >= 86400) {
$stringDhms = $stringD.$stringh.$stringm.$strings;
$stringDhm = $stringD.$stringh.$stringm;
$stringDh = $stringD.$stringh;
$stringHms = $stringH.$stringm.$strings;
$stringHm = $stringH.$stringm;
$stringMs = $stringM.$strings; }

if (($S >= 3600) && ($S < 86400)) {
$stringDhms = $stringH.$stringm.$strings;
$stringDhm = $stringH.$stringm;
$stringDh = $stringH;
$stringD = $stringH;
$stringHms = $stringH.$stringm.$strings;
$stringHm = $stringH.$stringm;
$stringMs = $stringM.$strings; }

if (($S >= 60) && ($S < 3600)) {
$stringDhms = $stringM.$strings;
$stringDhm = $stringM;
$stringDh = $stringM;
$stringD = $stringM;
$stringHms = $stringM.$strings;
$stringHm = $stringM;
$stringH = $stringM;
$stringMs = $stringM.$strings; }

if ($S < 60) {
$stringDhms = $stringS;
$stringDhm = $stringS;
$stringDh = $stringS;
$stringD = $stringS;
$stringHms = $stringS;
$stringHm = $stringS;
$stringH = $stringS;
$stringMs = $stringS;
$stringM = $stringS; }

$stringhms = $stringh.$stringm.$strings;
$stringhm = $stringh.$stringm;
$stringms = $stringm.$strings;

$timer = array(
'Dhms' => trim($stringDhms),
'Dhm' => trim($stringDhm),
'Dh' => trim($stringDh),
'D' => trim($stringD),
'Hms' => trim($stringHms),
'Hm' => trim($stringHm),
'H' => trim($stringH),
'Ms' => trim($stringMs),
'M' => trim($stringM),
'S' => trim($stringS),
'hms' => trim($stringhms),
'hm' => trim($stringhm),
'h' => trim($stringh),
'ms' => trim($stringms),
'm' => trim($stringm),
's' => trim($strings));