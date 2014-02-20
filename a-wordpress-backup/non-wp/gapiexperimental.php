<html>
	<head>
		<script>
			// bind the share js file to the parent if in iframe, otherwise err out
			if (window.self === window.top) {
				throw "Need to be in iFrame to get jQuery for ajax";
							
			}
					
		</script>
		<script   src="http://techshroom.com/non-wp/js/iframe_jq_share.js">
			
		</script>
		<?php
		$status = "Preparing js -> PHP transition" ?>
		<script>
			// add object.keys() - used later
			if (!Object.keys) Object.keys = function  (o) {
				if (o !== Object(o))    throw new TypeError('Object.keys called on a non-object');
				var   k=[],p;
				for (p in o) if (Object.prototype.hasOwnProperty.call(o,p)) k.push(p);
				return k;
				
			}
			// g-api related functions and vars
			// for adding and removing functions to call on refresh dynamically
			var   freflist = [], frefargs = [];
			// lists all issues from repo
			function   gapi_listissues(repo) {
							
			}
			// refreshes the functions in the list (calls them)
			function   gapi_refresh(functional_lists, functional_args) {
				if (functional_lists.length !== functional_args.length) {
					console.log("gapi_refresh err: lengths not equal");
					return;
									
				}
				for (var   i = 0;
				i < functional_lists.length;
				i++) {
					// use window as we treat these as if called from window
					functional_lists[i].apply(window, functional_args[i]);
									
				}
							
			}
			// when does the request rate limit reset?
			var   reqResetAsEpoch = 0;
			var   reqResetAsDate = null;
			// update rate limits
			function   rateLimitUpdate() {
				gajax("https://api.github.com/rate_limit", null, function  (){
					console.log("fetched limits");
				}, function  (x,e,s){
					console.log(e);
					console.log(s);
				});
								
			}
			// set reqResets
			function   recivedData(gdata) {
				reqResetAsEpoch = gdata.meta["X-RequestRate-Reset"];
				reqResetAsDate = Date(parseInt(reqRateAsEpoch) * 1000);
				console.log(gdata);
								
			}
			// end g-api section
			// ajax helper funcs
			// github ajax request
			function   gajax(url, settings, done, fail, always) {
				if (!settings) {
					settings = {
						
					}
					;
										
				}
				var   arr = settings.data;
				if(!arr) {
					arr = {
				}
					;
					
				}
				arr.api = 'api-3';
				arr.request = 'json';
				settings.data = arr;
				settings.dataType = "jsonp";
				return ajax_create(url, settings, done, fail, always).done(recivedData);
								
			}
			// All values can be undefined or null except for URL
			function   ajax_create(url, settings, done, fail, always) {
				if (!url) {
					throw "url cannot be null";
										
				}
				if (!done) {
					done = function  (){
						
					}
					;
										
				}
				if (!fail) {
					fail = function  (){
						
					}
					;
										
				}
				if (!always) {
					always = function  (){
						
					}
					;
										
				}
				if (!settings) {
					settings = {
						
					}
					;
										
				}
				var   arr = settings.data;
				if(!arr) {
					arr = {
				}
					;
					
				}
				arr.url = url;
				arr.type = settings.type  ||  "GET";
				settings.data = arr;
				settings.type = "GET";
				settings.url = "../../non-wp/gapibackend.php";
				return $.ajax(settings).done(done).fail(fail).always(always);
								
			}
			var   randomString = (function  () {
				// Define character classes to pick from randomly.
				var   uppers = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				var   lowers = 'abcdefghijklmnopqrstuvwxyz';
				var   numbers = '0123456789';
				var   specials = '_-|@.,?/!~#$%^&*(){}[]+=';
				var   charClasses = [uppers, lowers, numbers, specials];
				function   chooseRandom(x) {
					var   i = Math.floor(Math.random() * x.length);
					return (typeof(x)==='string') ? x.substr(i,1) : x[i];
										
				}
				// Define the function to actually generate a random string.
				return function  (maxLen) {
					maxLen = (maxLen   ||   36);
					// Append a random char from a random char class.
					var   str='', charClass;
					while (str.length < maxLen) {
						charClass = chooseRandom(charClasses);
						str += chooseRandom(charClass);
												
					}
					return str;
										
				}
								
			})();
			function   RGB2Color(r,g,b)            {
				return '#' + byte2Hex(r) + byte2Hex(g) + byte2Hex(b);
								
			}
			// needed for b2h
			var   nybHexString = "0123456789ABCDEF";
			function   byte2Hex(n) {
				return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
								
			}
			function   formatS(string) {
				var   len = newCharsCount;
				var   split = [];
				for (var   offset = 0, strLen = string.length;
				offset < strLen;
				offset += len) {
					split.push(string.slice(offset, len + offset));
										
				}
				string = "";
				for(var   i = 0;
				i < split.length;
				i++) {
					string += split[i] + "\n";
										
				}
				return string;
								
			}
			function   deformatS(string) {
				return string.replace("\n", "");
								
			}
			// frequencies for refreshes, in ms*5
			var   rt_freq = 10;
			var   rc_freq = 15;
			var   gapi_freq = 2;
			// refresh counter
			var   rpassed = 0;
			// for getting chars to use as max-char-size
			function   getchars() {
				var   wwidth = pagetext.clientWidth;
				console.log(wwidth);
				var   d = document.createElement('div');
				d.setAttribute('id', 'width-grabber');
				d.setAttribute('style', pagefont.getAttribute('style') +" display: inline-block; visibility:hidden;");
				d.innerHTML = 'A';
				document.body.appendChild(d);
				var   cwidth = d.clientWidth;
				$('#width-grabber').remove();
				return Math.floor(wwidth/cwidth);
								
			}
			window.onload = function   () {
				window.pagetop = document.getElementById("pagetop");
				window.pagetext = document.getElementById("pagetext");
				window.pagecolor = document.getElementById("pagecolor");
				window.pagefont = document.getElementById("pagefont");
				// chars per line
				window.newCharsCount = 0;
				window.newCharsCount = getchars();
				// current html text, without newlines
				window.ctext = randomString(newCharsCount * 35);
				function   next() {
					if (rpassed % rt_freq === 0) {
						random_text();
												
					}
					if (rpassed % rc_freq === 0) {
						random_color();
												
					}
					if (rpassed % gapi_freq === 0) {
						gapi_refresh(freflist, frefargs);
												
					}
					rpassed += 1;
										
				}
				function   random_text() {
					var   string = ctext, rchar = randomString(newCharsCount);
					string = rchar + string.substring(0, string.length - newCharsCount);
					ctext = string;
					pagetext.innerText = formatS(ctext);
										
				}
				var   rf, gf, bf;
				rf = gf = bf = 0.03594859014265239;
				var   i = 0;
				function   random_color() {
					var   red   = Math.sin(rf*i + 0) * 127 + 128;
					var   green = Math.sin(gf*i + 2*Math.PI/3) * 127 + 128;
					var   blue  = Math.sin(bf*i + 4*Math.PI/3) * 127 + 128;
					i++;
					pagecolor.setAttribute("style", "background-color: " + RGB2Color(red, green, blue) + ";");
					pagetext.setAttribute("style", "color: " + RGB2Color(255 - red, 255 - green, 255 - blue) + "; text-align: center;");
										
				}
				function   recursive() {
					next();
					setTimeout(function   () {
						recursive();
												
					}
					, 5);
										
				}
				// set a frame reference
				parent.parent.gapiframewin = window;
				// load data set
				pagetext.innerHTML = formatS(ctext);
				recursive();
				parent.iframeLoaded();
								
			}
						
		</script>
				
	</head>
	<body>
		<div   id="pagefont"   style="font-family: monospace, monospace; font-size: 1em;">
			<div   id="pagecolor"   style="background-color: #FFFFFF;">
				<center>
					<div   id="pagetop">
						<?php
						echo   $status ?>
												
					</div>
										
				</center>
				<div   id="pagetext"   style="color : #000000; text-align: center;">
										
				</div>
								
			</div>
						
		</div>
				
	</body>
	
</html>