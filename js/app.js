// Builds Manager © 2014 TechShroom Studios
define(["jquery" ,"js/xdomain.require.js"], function ($, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    appns.init = function () {
            var get = $.get(url);
            var $div = $('#builds-' + BUILD_DATA.owner + '-' + BUILD_DATA.repo);
            get.done(function (data) {
                $div.html($.parseHTML(data.results[0]));
            });
            get.fail(function () {
                $div.text("Failed GET from " + url);
            });
    }
    return appns;
});
