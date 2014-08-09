// Builds Manager © 2014 TechShroom Studios
define(["jquery" ,"js/xdomain.require.js"], function ($, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    function toggleFunc($it) {
        // $it -> button with span icon
        $it.children("span").toggleClass("glyphicon-plus glyphicon-minus");
        console.log($it.parent().next()[0]);
        $it.parent().next().toggle(400);
    };
    appns.init = function () {
        var divs = $('.build-div');
        divs.each(function (i, it) {
            var $it = $(it);
            var dir = $it.attr('rdir');
            console.log('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + dir + '/links.html');
            var get = $.get('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + dir + '/links.html');
            get.done(function (data) {
                $it.hide();
                $it.html($.parseHTML(data.results[0]));
                $it.children("ul").addClass("list-group");
                $it.children("ul").children("li").addClass("list-group-item");
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
