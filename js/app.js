// Builds Manager © 2014 TechShroom Studios
define(["jqui" ,"js/xdomain.require.js"], function ($, xd) {
    console.log($.ui);
    $.ajax = xd.wrapAjax($);
    var appns = {};
    function toggleFunc($it) {
        // $it -> button with span icon
        $it.children("span").toggleClass("glyphicon-plus glyphicon-minus");
        $it.parent().next().toggle(400);
    };
    appns.init = function () {
        var divs = $('.build-div');
        divs.each(function (i, it) {
            var $it = $(it);
            var dir = $it.attr('rdir');
            var get = $.get('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + dir + '/links.html');
            get.done(function (data) {
                $it.hide();
                $it.html($.parseHTML(data.results[0]));
                $it.children("ul").addClass("list-group");
                $it.children("ul").each(function (i, it2) {
                    var $it2 = $(it2);
                    $it2.children("li").addClass("list-group-item");
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
