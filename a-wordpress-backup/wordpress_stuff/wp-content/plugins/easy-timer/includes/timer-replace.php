<?php $timer = timer_string($S);

$content = str_replace('['.$prefix.'timer]', '['.$prefix.easy_timer_data('default_timer_prefix').'timer]', $content);
$content = str_replace('['.$prefix.'dhmstimer]', '<span class="dhmscount'.$way.'" data-time="'.$S.'">'.$timer['Dhms'].'</span>', $content);
$content = str_replace('['.$prefix.'dhmtimer]', '<span class="dhmcount'.$way.'" data-time="'.$S.'">'.$timer['Dhm'].'</span>', $content);
$content = str_replace('['.$prefix.'dhtimer]', '<span class="dhcount'.$way.'" data-time="'.$S.'">'.$timer['Dh'].'</span>', $content);
$content = str_replace('['.$prefix.'dtimer]', '<span class="dcount'.$way.'" data-time="'.$S.'">'.$timer['D'].'</span>', $content);
$content = str_replace('['.$prefix.'hmstimer]', '<span class="hmscount'.$way.'" data-time="'.$S.'">'.$timer['Hms'].'</span>', $content);
$content = str_replace('['.$prefix.'hmtimer]', '<span class="hmcount'.$way.'" data-time="'.$S.'">'.$timer['Hm'].'</span>', $content);
$content = str_replace('['.$prefix.'htimer]', '<span class="hcount'.$way.'" data-time="'.$S.'">'.$timer['H'].'</span>', $content);
$content = str_replace('['.$prefix.'mstimer]', '<span class="mscount'.$way.'" data-time="'.$S.'">'.$timer['Ms'].'</span>', $content);
$content = str_replace('['.$prefix.'mtimer]', '<span class="mcount'.$way.'" data-time="'.$S.'">'.$timer['M'].'</span>', $content);
$content = str_replace('['.$prefix.'stimer]', '<span class="scount'.$way.'" data-time="'.$S.'">'.$timer['S'].'</span>', $content);

$content = str_replace('['.$prefix.'rtimer]', '['.$prefix.easy_timer_data('default_timer_prefix').'rtimer]', $content);
$content = str_replace('['.$prefix.'dhmsrtimer]', '<span class="dhmscount'.$way.'" data-time="'.$S.'">'.$timer['Dhms'].'</span>', $content);
$content = str_replace('['.$prefix.'dhmrtimer]', '<span class="dhmcount'.$way.'" data-time="'.$S.'">'.$timer['Dhm'].'</span>', $content);
$content = str_replace('['.$prefix.'dhrtimer]', '<span class="dhcount'.$way.'" data-time="'.$S.'">'.$timer['Dh'].'</span>', $content);
$content = str_replace('['.$prefix.'drtimer]', '<span class="dcount'.$way.'" data-time="'.$S.'">'.$timer['D'].'</span>', $content);
$content = str_replace('['.$prefix.'hmsrtimer]', '<span class="hmsrcount'.$way.'" data-time="'.$S.'">'.$timer['hms'].'</span>', $content);
$content = str_replace('['.$prefix.'hmrtimer]', '<span class="hmrcount'.$way.'" data-time="'.$S.'">'.$timer['hm'].'</span>', $content);
$content = str_replace('['.$prefix.'hrtimer]', '<span class="hrcount'.$way.'" data-time="'.$S.'">'.$timer['h'].'</span>', $content);
$content = str_replace('['.$prefix.'msrtimer]', '<span class="msrcount'.$way.'" data-time="'.$S.'">'.$timer['ms'].'</span>', $content);
$content = str_replace('['.$prefix.'mrtimer]', '<span class="mrcount'.$way.'" data-time="'.$S.'">'.$timer['m'].'</span>', $content);
$content = str_replace('['.$prefix.'srtimer]', '<span class="srcount'.$way.'" data-time="'.$S.'">'.$timer['s'].'</span>', $content);