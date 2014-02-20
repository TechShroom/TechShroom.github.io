function trim(string) {
return string.replace(/^\s+/g,'').replace(/\s+$/g,''); } 


function timer_string(S, format) {
var D = Math.floor(S/86400);
var H = Math.floor(S/3600);
var M = Math.floor(S/60);
var h = H - 24*D;
var m = M - 60*H;
var s = S - 60*M;

var stringD = string0day;
var stringH = string0hour;
var stringM = string0minute;
var stringS = string0second;
var stringh = string0hour;
var stringm = string0minute;
var strings = string0second;

if (D == 1) { stringD = string1day; } else if (D > 1) { stringD = stringNdays.replace('[N]', D); }
if (H == 1) { stringH = string1hour; } else if (H > 1) { stringH = stringNhours.replace('[N]', H); }
if (M == 1) { stringM = string1minute; } else if (M > 1) { stringM = stringNminutes.replace('[N]', M); }
if (S == 1) { stringS = string1second; } else if (S > 1) { stringS = stringNseconds.replace('[N]', S); }
if (h == 1) { stringh = string1hour; } else if (h > 1) { stringh = stringNhours.replace('[N]', h); }
if (m == 1) { stringm = string1minute; } else if (m > 1) { stringm = stringNminutes.replace('[N]', m); }
if (s == 1) { strings = string1second; } else if (s > 1) { strings = stringNseconds.replace('[N]', s); }

if (S >= 86400) {
var stringDhms = stringD+stringh+stringm+strings;
var stringDhm = stringD+stringh+stringm;
var stringDh = stringD+stringh;
var stringHms = stringH+stringm+strings;
var stringHm = stringH+stringm;
var stringMs = stringM+strings; }

if ((S >= 3600) && (S < 86400)) {
var stringDhms = stringH+stringm+strings;
var stringDhm = stringH+stringm;
var stringDh = stringH;
var stringD = stringH;
var stringHms = stringH+stringm+strings;
var stringHm = stringH+stringm;
var stringMs = stringM+strings; }

if ((S >= 60) && (S < 3600)) {
var stringDhms = stringM+strings;
var stringDhm = stringM;
var stringDh = stringM;
var stringD = stringM;
var stringHms = stringM+strings;
var stringHm = stringM;
var stringH = stringM;
var stringMs = stringM+strings; }

if (S < 60) {
var stringDhms = stringS;
var stringDhm = stringS;
var stringDh = stringS;
var stringD = stringS;
var stringHms = stringS;
var stringHm = stringS;
var stringH = stringS;
var stringMs = stringS;
var stringM = stringS; }

var stringhms = stringh+stringm+strings;
var stringhm = stringh+stringm;
var stringms = stringm+strings;

switch (format) {
case 'dhms': return trim(stringDhms);
case 'dhm': return trim(stringDhm);
case 'dh': return trim(stringDh);
case 'd': return trim(stringD);
case 'hms': return trim(stringHms);
case 'hm': return trim(stringHm);
case 'h': return trim(stringH);
case 'ms': return trim(stringMs);
case 'm': return trim(stringM);
case 's': return trim(stringS);
case 'hmsr': return trim(stringhms);
case 'hmr': return trim(stringhm);
case 'hr': return trim(stringh);
case 'msr': return trim(stringms);
case 'mr': return trim(stringm);
case 'sr': return trim(strings);
default: return trim(stringDhms); } }


function dhmstimer_decrease(el) {
var S = dhmscountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
dhmscountdown[el].setAttribute('data-time', S);
dhmscountdown[el].innerHTML = timer_string(S, 'dhms'); }

function dhmtimer_decrease(el) {
var S = dhmcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
dhmcountdown[el].setAttribute('data-time', S);
dhmcountdown[el].innerHTML = timer_string(S, 'dhm'); }

function dhtimer_decrease(el) {
var S = dhcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
dhcountdown[el].setAttribute('data-time', S);
dhcountdown[el].innerHTML = timer_string(S, 'dh'); }

function dtimer_decrease(el) {
var S = dcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
dcountdown[el].setAttribute('data-time', S);
dcountdown[el].innerHTML = timer_string(S, 'd'); }

function hmstimer_decrease(el) {
var S = hmscountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
hmscountdown[el].setAttribute('data-time', S);
hmscountdown[el].innerHTML = timer_string(S, 'hms'); }

function hmtimer_decrease(el) {
var S = hmcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
hmcountdown[el].setAttribute('data-time', S);
hmcountdown[el].innerHTML = timer_string(S, 'hm'); }

function htimer_decrease(el) {
var S = hcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
hcountdown[el].setAttribute('data-time', S);
hcountdown[el].innerHTML = timer_string(S, 'h'); }

function mstimer_decrease(el) {
var S = mscountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
mscountdown[el].setAttribute('data-time', S);
mscountdown[el].innerHTML = timer_string(S, 'ms'); }

function mtimer_decrease(el) {
var S = mcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
mcountdown[el].setAttribute('data-time', S);
mcountdown[el].innerHTML = timer_string(S, 'm'); }

function stimer_decrease(el) {
var S = scountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
scountdown[el].setAttribute('data-time', S);
scountdown[el].innerHTML = timer_string(S, 's'); }

function hmsrtimer_decrease(el) {
var S = hmsrcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
hmsrcountdown[el].setAttribute('data-time', S);
hmsrcountdown[el].innerHTML = timer_string(S, 'hmsr'); }

function hmrtimer_decrease(el) {
var S = hmrcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
hmrcountdown[el].setAttribute('data-time', S);
hmrcountdown[el].innerHTML = timer_string(S, 'hmr'); }

function hrtimer_decrease(el) {
var S = hrcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
hrcountdown[el].setAttribute('data-time', S);
hrcountdown[el].innerHTML = timer_string(S, 'hr'); }

function msrtimer_decrease(el) {
var S = msrcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
msrcountdown[el].setAttribute('data-time', S);
msrcountdown[el].innerHTML = timer_string(S, 'msr'); }

function mrtimer_decrease(el) {
var S = mrcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
mrcountdown[el].setAttribute('data-time', S);
mrcountdown[el].innerHTML = timer_string(S, 'mr'); }

function srtimer_decrease(el) {
var S = srcountdown[el].getAttribute('data-time') - 1;
if (S <= 0) { window.location.reload(); }
srcountdown[el].setAttribute('data-time', S);
srcountdown[el].innerHTML = timer_string(S, 'sr'); }


function dhmstimer_increase(el) {
var S = dhmscountup[el].getAttribute('data-time') - (-1);
dhmscountup[el].setAttribute('data-time', S);
dhmscountup[el].innerHTML = timer_string(S, 'dhms'); }

function dhmtimer_increase(el) {
var S = dhmcountup[el].getAttribute('data-time') - (-1);
dhmcountup[el].setAttribute('data-time', S);
dhmcountup[el].innerHTML = timer_string(S, 'dhm'); }

function dhtimer_increase(el) {
var S = dhcountup[el].getAttribute('data-time') - (-1);
dhcountup[el].setAttribute('data-time', S);
dhcountup[el].innerHTML = timer_string(S, 'dh'); }

function dtimer_increase(el) {
var S = dcountup[el].getAttribute('data-time') - (-1);
dcountup[el].setAttribute('data-time', S);
dcountup[el].innerHTML = timer_string(S, 'd'); }

function hmstimer_increase(el) {
var S = hmscountup[el].getAttribute('data-time') - (-1);
hmscountup[el].setAttribute('data-time', S);
hmscountup[el].innerHTML = timer_string(S, 'hms'); }

function hmtimer_increase(el) {
var S = hmcountup[el].getAttribute('data-time') - (-1);
hmcountup[el].setAttribute('data-time', S);
hmcountup[el].innerHTML = timer_string(S, 'hm'); }

function htimer_increase(el) {
var S = hcountup[el].getAttribute('data-time') - (-1);
hcountup[el].setAttribute('data-time', S);
hcountup[el].innerHTML = timer_string(S, 'h'); }

function mstimer_increase(el) {
var S = mscountup[el].getAttribute('data-time') - (-1);
mscountup[el].setAttribute('data-time', S);
mscountup[el].innerHTML = timer_string(S, 'ms'); }

function mtimer_increase(el) {
var S = mcountup[el].getAttribute('data-time') - (-1);
mcountup[el].setAttribute('data-time', S);
mcountup[el].innerHTML = timer_string(S, 'm'); }

function stimer_increase(el) {
var S = scountup[el].getAttribute('data-time') - (-1);
scountup[el].setAttribute('data-time', S);
scountup[el].innerHTML = timer_string(S, 's'); }

function hmsrtimer_increase(el) {
var S = hmsrcountup[el].getAttribute('data-time') - (-1);
hmsrcountup[el].setAttribute('data-time', S);
hmsrcountup[el].innerHTML = timer_string(S, 'hmsr'); }

function hmrtimer_increase(el) {
var S = hmrcountup[el].getAttribute('data-time') - (-1);
hmrcountup[el].setAttribute('data-time', S);
hmrcountup[el].innerHTML = timer_string(S, 'hmr'); }

function hrtimer_increase(el) {
var S = hrcountup[el].getAttribute('data-time') - (-1);
hrcountup[el].setAttribute('data-time', S);
hrcountup[el].innerHTML = timer_string(S, 'hr'); }

function msrtimer_increase(el) {
var S = msrcountup[el].getAttribute('data-time') - (-1);
msrcountup[el].setAttribute('data-time', S);
msrcountup[el].innerHTML = timer_string(S, 'msr'); }

function mrtimer_increase(el) {
var S = mrcountup[el].getAttribute('data-time') - (-1);
mrcountup[el].setAttribute('data-time', S);
mrcountup[el].innerHTML = timer_string(S, 'mr'); }

function srtimer_increase(el) {
var S = srcountup[el].getAttribute('data-time') - (-1);
srcountup[el].setAttribute('data-time', S);
srcountup[el].innerHTML = timer_string(S, 'sr'); }


function clock_update(offset, format) {
var T = new Date();

if (offset == 'local') {
var H = T.getHours();
var m = T.getMinutes();
var s = T.getSeconds(); }

else {
var H = T.getUTCHours();
var m = T.getUTCMinutes();
var s = T.getUTCSeconds();

if (offset != 0) {
if (offset > 0) { offset = offset%24; }
else { offset = 24 - (-offset)%24; }

var S = (3600*(H + offset) + 60*m + s)%86400;
var H = Math.floor(S/3600);
var M = Math.floor(S/60);
var m = M - 60*H;
var s = S - 60*M; } }

if (H < 10) { H = '0'+H; }
if (m < 10) { m = '0'+m; }
if (s < 10) { s = '0'+s; }

switch (format) {
case 'hm': return H+':'+m; break;
case 'hms': return H+':'+m+':'+s; break;
default: return H+':'+m; } }


function hmclock_update(el) {
var offset = hmclock[el].getAttribute('data-offset');
hmclock[el].innerHTML = clock_update(offset, 'hm'); }

function hmsclock_update(el) {
var offset = hmsclock[el].getAttribute('data-offset');
hmsclock[el].innerHTML = clock_update(offset, 'hms'); }

function localhmclock_update(el) {
localhmclock[el].innerHTML = clock_update('local', 'hm'); }

function localhmsclock_update(el) {
localhmsclock[el].innerHTML = clock_update('local', 'hms'); }


function localyear_update(format) {
var T = new Date();
var year4 = T.getFullYear();
var year2 = (year4)%100;

switch (format) {
case '2': return year2;
case '4': return year4;
default: return year4; } }


function local2year_update(el) {
local2year[el].innerHTML = localyear_update('2'); }

function local4year_update(el) {
local4year[el].innerHTML = localyear_update('4'); }


function localisoyear_update(el) {
var T = new Date();
var isoyear = T.getFullYear();
var month = T.getMonth();
var monthday = T.getDate();
var weekday = T.getDay(); if (weekday == 0) { weekday = 7; }
if ((month == 0) && (weekday - monthday >= 4)) { isoyear = isoyear - 1; }
if ((month == 11) && (monthday - weekday >= 28)) { isoyear = isoyear + 1; }
localisoyear[el].innerHTML = isoyear; }


function localyearweek_update(el) {
var T = new Date();
var year = T.getFullYear();
var month = T.getMonth();
var monthday = T.getDate();
var weekday = T.getDay(); if (weekday == 0) { weekday = 7; }
var B = 0; if (((year%4 == 0) && (year%100 != 0)) || (year%400 == 0)) { B = 1; }
var array = new Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);

if (month <= 1) { var N = 10 + array[month] + monthday - weekday; }
else { var N = B + 10 + array[month] + monthday - weekday; }
var yearweek = Math.floor(N/7);

if (yearweek == 0) {
B = 0; if ((((year - 1)%4 == 0) && ((year - 1)%100 != 0)) || ((year - 1)%400 == 0)) { B = 1; }
N = B + 375 + array[month] + monthday - weekday;
yearweek = Math.floor(N/7); }

if ((month == 11) && (monthday - weekday >= 28)) { yearweek = 1; }

localyearweek[el].innerHTML = yearweek; }


function localyearday_update(el) {
var T = new Date();
var year = T.getFullYear();
var month = T.getMonth();
var monthday = T.getDate();
var B = 0; if (((year%4 == 0) && (year%100 != 0)) || (year%400 == 0)) { B = 1; }
var array = new Array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);

if (month <= 1) { var yearday = array[month] + monthday; }
else { var yearday = B + array[month] + monthday; }

localyearday[el].innerHTML = yearday; }


function localmonth_update(format) {
var T = new Date();
var month1 = T.getMonth() + 1;
var month2 = month1;
if (month1 < 10) { month2 = '0'+month1; }
var month = stringmonth[month1].substr(0, 1)+stringmonth[month1].substr(1).toLowerCase();
var lowermonth = stringmonth[month1].toLowerCase();
var uppermonth = stringmonth[month1];

switch (format) {
case '1': return month1;
case '2': return month2;
case '': return month;
case 'lower': return lowermonth;
case 'upper': return uppermonth;
default: return month; } }


function localdefaultmonth_update(el) {
localmonth[el].innerHTML = localmonth_update(''); }

function local1month_update(el) {
local1month[el].innerHTML = localmonth_update('1'); }

function local2month_update(el) {
local2month[el].innerHTML = localmonth_update('2'); }

function locallowermonth_update(el) {
locallowermonth[el].innerHTML = localmonth_update('lower'); }

function localuppermonth_update(el) {
localuppermonth[el].innerHTML = localmonth_update('upper'); }


function localmonthday_update(format) {
var T = new Date();
var monthday1 = T.getDate();
var monthday2 = monthday1;
if (monthday1 < 10) { monthday2 = '0'+monthday1; }

switch (format) {
case '1': return monthday1;
case '2': return monthday2;
default: return monthday1; } }


function local1monthday_update(el) {
local1monthday[el].innerHTML = localmonthday_update('1'); }

function local2monthday_update(el) {
local2monthday[el].innerHTML = localmonthday_update('2'); }


function localweekday_update(format) {
var T = new Date();
var weekday1 = T.getDay();
var weekday = stringweekday[weekday1].substr(0, 1)+stringweekday[weekday1].substr(1).toLowerCase();
var lowerweekday = stringweekday[weekday1].toLowerCase();
var upperweekday = stringweekday[weekday1];

switch (format) {
case '': return weekday;
case 'lower': return lowerweekday;
case 'upper': return upperweekday;
default: return weekday; } }


function localdefaultweekday_update(el) {
localweekday[el].innerHTML = localweekday_update(''); }

function locallowerweekday_update(el) {
locallowerweekday[el].innerHTML = localweekday_update('lower'); }

function localupperweekday_update(el) {
localupperweekday[el].innerHTML = localweekday_update('upper'); }


function localtimezone_update(el) {
var offset = -((new Date()).getTimezoneOffset())/60;
if (offset == 0) { var timezone = 'UTC'; }
if (offset > 0) { var timezone = 'UTC+'+offset; }
if (offset < 0) { var timezone = 'UTC'+offset; }
localtimezone[el].innerHTML = timezone; }


function start() {
if (document.getElementsByClassName === undefined) {
document.getElementsByClassName = function(className) {
var hasClassName = new RegExp('(?:^|\\s)' + className + '(?:$|\\s)');
var allElements = document.getElementsByTagName('*');
var results = [];
var element;
for (i = 0; (element = allElements[i]) != null; i++) {
var elementClass = element.className;
if (elementClass && elementClass.indexOf(className) != -1 && hasClassName.test(elementClass)) {
results.push(element); } }
return results; } }

dhmscountdown = document.getElementsByClassName('dhmscountdown');
for (el in dhmscountdown) { if (parseInt(el) + 1) { setInterval('dhmstimer_decrease('+el+')', 1000); } }
dhmcountdown = document.getElementsByClassName('dhmcountdown');
for (el in dhmcountdown) { if (parseInt(el) + 1) { setInterval('dhmtimer_decrease('+el+')', 1000); } }
dhcountdown = document.getElementsByClassName('dhcountdown');
for (el in dhcountdown) { if (parseInt(el) + 1) { setInterval('dhtimer_decrease('+el+')', 1000); } }
dcountdown = document.getElementsByClassName('dcountdown');
for (el in dcountdown) { if (parseInt(el) + 1) { setInterval('dtimer_decrease('+el+')', 1000); } }
hmscountdown = document.getElementsByClassName('hmscountdown');
for (el in hmscountdown) { if (parseInt(el) + 1) { setInterval('hmstimer_decrease('+el+')', 1000); } }
hmcountdown = document.getElementsByClassName('hmcountdown');
for (el in hmcountdown) { if (parseInt(el) + 1) { setInterval('hmtimer_decrease('+el+')', 1000); } }
hcountdown = document.getElementsByClassName('hcountdown');
for (el in hcountdown) { if (parseInt(el) + 1) { setInterval('htimer_decrease('+el+')', 1000); } }
mscountdown = document.getElementsByClassName('mscountdown');
for (el in mscountdown) { if (parseInt(el) + 1) { setInterval('mstimer_decrease('+el+')', 1000); } }
mcountdown = document.getElementsByClassName('mcountdown');
for (el in mcountdown) { if (parseInt(el) + 1) { setInterval('mtimer_decrease('+el+')', 1000); } }
scountdown = document.getElementsByClassName('scountdown');
for (el in scountdown) { if (parseInt(el) + 1) { setInterval('stimer_decrease('+el+')', 1000); } }
hmsrcountdown = document.getElementsByClassName('hmsrcountdown');
for (el in hmsrcountdown) { if (parseInt(el) + 1) { setInterval('hmsrtimer_decrease('+el+')', 1000); } }
hmrcountdown = document.getElementsByClassName('hmrcountdown');
for (el in hmrcountdown) { if (parseInt(el) + 1) { setInterval('hmrtimer_decrease('+el+')', 1000); } }
hrcountdown = document.getElementsByClassName('hrcountdown');
for (el in hrcountdown) { if (parseInt(el) + 1) { setInterval('hrtimer_decrease('+el+')', 1000); } }
msrcountdown = document.getElementsByClassName('msrcountdown');
for (el in msrcountdown) { if (parseInt(el) + 1) { setInterval('msrtimer_decrease('+el+')', 1000); } }
mrcountdown = document.getElementsByClassName('mrcountdown');
for (el in mrcountdown) { if (parseInt(el) + 1) { setInterval('mrtimer_decrease('+el+')', 1000); } }
srcountdown = document.getElementsByClassName('srcountdown');
for (el in srcountdown) { if (parseInt(el) + 1) { setInterval('srtimer_decrease('+el+')', 1000); } }
dhmscountup = document.getElementsByClassName('dhmscountup');
for (el in dhmscountup) { if (parseInt(el) + 1) { setInterval('dhmstimer_increase('+el+')', 1000); } }
dhmcountup = document.getElementsByClassName('dhmcountup');
for (el in dhmcountup) { if (parseInt(el) + 1) { setInterval('dhmtimer_increase('+el+')', 1000); } }
dhcountup = document.getElementsByClassName('dhcountup');
for (el in dhcountup) { if (parseInt(el) + 1) { setInterval('dhtimer_increase('+el+')', 1000); } }
dcountup = document.getElementsByClassName('dcountup');
for (el in dcountup) { if (parseInt(el) + 1) { setInterval('dtimer_increase('+el+')', 1000); } }
hmscountup = document.getElementsByClassName('hmscountup');
for (el in hmscountup) { if (parseInt(el) + 1) { setInterval('hmstimer_increase('+el+')', 1000); } }
hmcountup = document.getElementsByClassName('hmcountup');
for (el in hmcountup) { if (parseInt(el) + 1) { setInterval('hmtimer_increase('+el+')', 1000); } }
hcountup = document.getElementsByClassName('hcountup');
for (el in hcountup) { if (parseInt(el) + 1) { setInterval('htimer_increase('+el+')', 1000); } }
mscountup = document.getElementsByClassName('mscountup');
for (el in mscountup) { if (parseInt(el) + 1) { setInterval('mstimer_increase('+el+')', 1000); } }
mcountup = document.getElementsByClassName('mcountup');
for (el in mcountup) { if (parseInt(el) + 1) { setInterval('mtimer_increase('+el+')', 1000); } }
scountup = document.getElementsByClassName('scountup');
for (el in scountup) { if (parseInt(el) + 1) { setInterval('stimer_increase('+el+')', 1000); } }
hmsrcountup = document.getElementsByClassName('hmsrcountup');
for (el in hmsrcountup) { if (parseInt(el) + 1) { setInterval('hmsrtimer_increase('+el+')', 1000); } }
hmrcountup = document.getElementsByClassName('hmrcountup');
for (el in hmrcountup) { if (parseInt(el) + 1) { setInterval('hmrtimer_increase('+el+')', 1000); } }
hrcountup = document.getElementsByClassName('hrcountup');
for (el in hrcountup) { if (parseInt(el) + 1) { setInterval('hrtimer_increase('+el+')', 1000); } }
msrcountup = document.getElementsByClassName('msrcountup');
for (el in msrcountup) { if (parseInt(el) + 1) { setInterval('msrtimer_increase('+el+')', 1000); } }
mrcountup = document.getElementsByClassName('mrcountup');
for (el in mrcountup) { if (parseInt(el) + 1) { setInterval('mrtimer_increase('+el+')', 1000); } }
srcountup = document.getElementsByClassName('srcountup');
for (el in srcountup) { if (parseInt(el) + 1) { setInterval('srtimer_increase('+el+')', 1000); } }
hmclock = document.getElementsByClassName('hmclock');
for (el in hmclock) { if (parseInt(el) + 1) { setInterval('hmclock_update('+el+')', 1000); } }
hmsclock = document.getElementsByClassName('hmsclock');
for (el in hmsclock) { if (parseInt(el) + 1) { setInterval('hmsclock_update('+el+')', 1000); } }
localhmclock = document.getElementsByClassName('localhmclock');
for (el in localhmclock) { if (parseInt(el) + 1) { setInterval('localhmclock_update('+el+')', 1000); } }
localhmsclock = document.getElementsByClassName('localhmsclock');
for (el in localhmsclock) { if (parseInt(el) + 1) { setInterval('localhmsclock_update('+el+')', 1000); } }
local2year = document.getElementsByClassName('local2year');
for (el in local2year) { if (parseInt(el) + 1) { setInterval('local2year_update('+el+')', 1000); } }
local4year = document.getElementsByClassName('local4year');
for (el in local4year) { if (parseInt(el) + 1) { setInterval('local4year_update('+el+')', 1000); } }
localisoyear = document.getElementsByClassName('localisoyear');
for (el in localisoyear) { if (parseInt(el) + 1) { setInterval('localisoyear_update('+el+')', 1000); } }
localyearweek = document.getElementsByClassName('localyearweek');
for (el in localyearweek) { if (parseInt(el) + 1) { setInterval('localyearweek_update('+el+')', 1000); } }
localyearday = document.getElementsByClassName('localyearday');
for (el in localyearday) { if (parseInt(el) + 1) { setInterval('localyearday_update('+el+')', 1000); } }
localmonth = document.getElementsByClassName('localmonth');
for (el in localmonth) { if (parseInt(el) + 1) { setInterval('localdefaultmonth_update('+el+')', 1000); } }
local1month = document.getElementsByClassName('local1month');
for (el in local1month) { if (parseInt(el) + 1) { setInterval('local1month_update('+el+')', 1000); } }
local2month = document.getElementsByClassName('local2month');
for (el in local2month) { if (parseInt(el) + 1) { setInterval('local2month_update('+el+')', 1000); } }
locallowermonth = document.getElementsByClassName('locallowermonth');
for (el in locallowermonth) { if (parseInt(el) + 1) { setInterval('locallowermonth_update('+el+')', 1000); } }
localuppermonth = document.getElementsByClassName('localuppermonth');
for (el in localuppermonth) { if (parseInt(el) + 1) { setInterval('localuppermonth_update('+el+')', 1000); } }
local1monthday = document.getElementsByClassName('local1monthday');
for (el in local1monthday) { if (parseInt(el) + 1) { setInterval('local1monthday_update('+el+')', 1000); } }
local2monthday = document.getElementsByClassName('local2monthday');
for (el in local2monthday) { if (parseInt(el) + 1) { setInterval('local2monthday_update('+el+')', 1000); } }
localweekday = document.getElementsByClassName('localweekday');
for (el in localweekday) { if (parseInt(el) + 1) { setInterval('localdefaultweekday_update('+el+')', 1000); } }
locallowerweekday = document.getElementsByClassName('locallowerweekday');
for (el in locallowerweekday) { if (parseInt(el) + 1) { setInterval('locallowerweekday_update('+el+')', 1000); } }
localupperweekday = document.getElementsByClassName('localupperweekday');
for (el in localupperweekday) { if (parseInt(el) + 1) { setInterval('localupperweekday_update('+el+')', 1000); } }
localtimezone = document.getElementsByClassName('localtimezone');
for (el in localtimezone) { if (parseInt(el) + 1) { setInterval('localtimezone_update('+el+')', 1000); } } }


if (typeof(document.addEventListener) == 'function') {
document.addEventListener('DOMContentLoaded', start, false); }
else { window.onload = start; }