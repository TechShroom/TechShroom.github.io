// Builds Manager © 2014 TechShroom Studios
define(["jquery" ,"js/xdomain.require.js"], function ($, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    appns.init = function () {
        $('.build-div').each(function (i, it) {
            var $it = $(it);
            var dir = $it.attr('dir');
            console.log('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + dir + '/links.html');
            var get = $.get('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + dir + '/links.html');
            get.done(function (data) {
                $it.html($.parseHTML(data.results[0]));
            });
            get.fail(function () {
                $it.text("Failed GET from " + url);
            });
        });
    }
    return appns;
});
