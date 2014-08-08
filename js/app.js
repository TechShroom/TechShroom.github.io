// Builds Manager © 2014 TechShroom Studios
define(["jquery" ,"xdomain.require.js"], function ($, xd) {
    $.ajax = xd.wrapAjax($);
    var appns = {};
    (function () {
        appns.init = function () {
            $.post( "http://techshroom.com/non-wp/uploads/manager/commit.php", { 'r|e|p|o': BUILD_DATA.repo, 'o|w|n|er': BUILD_DATA.owner, 'r|e|p|odir': BUILD_DATA.dir})
                .done(function(data) {
                    $('#builds-' + BUILD_DATA.owner + '-' + BUILD_DATA.repo).html(data);
                });
        };
    })();
    return appns;
});
