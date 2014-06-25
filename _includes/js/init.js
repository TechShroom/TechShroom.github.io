requirejs.config({
    // "enforceDefine": true,
    "paths": {
        "jquery": ["//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min"]
    }
});

requirejs(["jquery", "app"], function ($, app) {
    $(function () {
        app.init();
    });
});
