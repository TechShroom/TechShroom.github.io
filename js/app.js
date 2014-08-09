// Builds Manager © 2014 TechShroom Studios
define(["jqui" ,"js/xdomain.require.js"], function ($undef, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    function toggleFunc($it) {
        if ($it.data('moving')) {
            // prevent derps
            return;
        }
        $it.data('moving', true);
        $it.children("span").toggleClass("glyphicon-plus glyphicon-minus");
        $it.parent().next().toggle("blind", {}, 500, function () {
            $it.data('moving', false);
        });
    };
    appns.init = function () {
        var divs = $('.build-div');
        divs.each(function (i, it) {
            var $it = $(it);
            var $ittext = $it.text();
            var counter = 0;
            var timer = setInterval(function () {
                var dots = "";
                for (var i = 0; i < counter; i++) {
                    dots = dots + '.';
                }
                $it.text($ittext + dots);
                counter++;
                if (counter > 5) {
                    counter = 0;
                }
            }, 150);
            
            var dir = $it.attr('rdir');
            var get = $.get('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + dir + '/links.html');
            get.always(function () {
                clearInterval(timer);
            });
            get.done(function (data) {
                $it.hide();
                $it.html($.parseHTML(data.results[0]));
                $it.children("ul").addClass("list-group");
                var firstLi = $child[0];
                var firstLiText = "no first";
                var firstLiLink = "javascript:alert('Broken repo')";
                $it.children("ul").each(function (j, it2) {
                    var $it2 = $(it2);
                    var $child = $it2.children("li");
                    $child.addClass("list-group-item");
                    $child.each(function (k, it3) {
                        $(it3).children("a").each(function (x, it4) {
                            var $it4 = $(it4);
                            
                            var shaSize = 7;
                            var inverseShaSize = 40 - 7;
                            $it4.text($it4.text().replace(new RegExp("([a-fA-F0-9]{"
                                                                    +shaSize+","+shaSize+
                                                                    "})[a-fA-F0-9]{"
                                                                    +inverseShaSize+","+inverseShaSize+
                                                                    "}"), "$1"));
                            if (it3 === firstLi) {
                                firstLiText = $it4.text();
                                firstLiLink = $it4.attr('href');
                            }
                        });
                    });
                    $it2.prepend('<li class="list-group-item><a href="'+
                                                            firstLiLink+
                                                            '">Latest ('+
                                                            firstLiText+
                                                            ")</a>"+
                                                            $(firstLi).text()+
                                                            "</li>");
                });
            });
            get.fail(function () {
                $it.text("Failed GET from " + url);
            });
        });
        var btns = $('button.build-toggle');
        btns.click(function () {
            toggleFunc($(this));
        });
    }
    return appns;
});
