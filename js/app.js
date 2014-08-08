// Builds Manager © 2014 TechShroom Studios
define(["jquery" ,"js/xdomain.require.js"], function ($, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    appns.init = function () {
        $('.build-divs').each(function (i, it) {
            console.log(it);
            var get = $.get('http://techshroom.com/non-wp/uploads/travis-ci/commital/' + BUILD_DATA.dir + '/links.html');
            var $div = $('#builds-' + BUILD_DATA.owner + '-' + BUILD_DATA.repo);
            get.done(function (data) {
                $div.html($.parseHTML(data.results[0]));
            });
            get.fail(function () {
                $div.text("Failed GET from " + url);
            });
        });
    }
    return appns;
});
