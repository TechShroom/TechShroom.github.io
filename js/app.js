// Builds Manager © 2014 TechShroom Studios
define(["jquery" ,"js/xdomain.require.js"], function ($, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    function toggleFunc($it) {
        // $it -> button with span icon
        $it.children("span").toggleClass("glyphicon-plus glyphicon-minus");
        console.log($it.next()[0]);
        $it.next().toggle();
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
