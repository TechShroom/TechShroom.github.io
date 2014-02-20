<html><head>
<script type='text/javascript' src='http://techshroom.com/wordpress_stuff/wp-includes/js/jquery/jquery.js?ver=1.10.2'></script>
<script>
var funcfield = null, argsfield = null, response;
function insert(data) {
    response.innerHTML = data;
}
function get() {
    func = funcfield.value, args = argsfield.value.split(', ');
    jQuery.get('phpinteractive_backend.php', {'func[]':func}, insert)
    .fail(function(x,s,e){console.log(x);console.log(s);console.log(e)});
}
function onloadcomplete() {
    funcfield = document.getElementById("func");
    argsfield = document.getElementById("args");
    response = document.getElementById("response");
}
</script>
</head>
<body onload="onloadcomplete()">
<center>
<form name="interact" action="javascript:get()">
PHP: <input id="func" type="text" name="php"><input id="args" type="text" name="php">
<input type="submit" value="Submit">
</form>
<div id="response">
</div>
</center>
</body></html>