function PjaxDynamicScriptLoader() {
    if (arguments.callee._singletonInstance) {
        return arguments.callee._singletonInstance;
    }

    arguments.callee._singletonInstance = this;

    var instance = arguments.callee._singletonInstance;

    this.loadedScripts = [];
    this.loadedCssFile = [];


    $('script').each(function (i, scriptTag) {
        var src = $(scriptTag).attr('src');
        if (src) {
            instance.loadedScripts.push(src);
        }
    });

    $('link').each(function (i, linkTag) {
        var src = $(linkTag).attr('src');
        var rel = $(linkTag).attr('rel');
        if (rel === 'stylesheet' && src) {
            instance.loadedCssFile.push(src);
        }
    });

    this.init = function (selector) {

        $(selector).on('pjax:end', function (e) {
            try {
                $(e.target).find('.js-pjax-scripts').each(function (index, element) {
                    var positions = JSON.parse($(element).html());
                    
                    if (positions.cssFiles) {
                        $(Object.keys(positions.cssFiles)).each(function (k, cssFile) {
                            if ($.inArray(cssFile, instance.loadedCssFile) === -1) {
                                instance.loadedCssFile.push(cssFile);
                                $("<link/>", {
                                    rel: "stylesheet",
                                    type: "text/css",
                                    href: cssFile
                                }).appendTo("head");
                            }
                        });
                    }
                    
                    var jsFiles = [];
                    var js = '';
                    $(Object.keys(positions)).each(function (i, position) {
                        if (positions[position].jsFiles) {
                            $(Object.keys(positions[position].jsFiles)).each(function (j, key) {
                                var file = positions[position].jsFiles[key];
                                if ($.inArray(file.url, instance.loadedScripts) === -1) {
                                    jsFiles.push(file.url);
                                    instance.loadedScripts.push(file.url);
                                }
                            });
                        }
                        if (positions[position].js) {
                            js += positions[position].js;
                        }
                    });
                    if (jsFiles.length) {
                        $script(jsFiles, function () {
                            if (js) {
                                eval(js);
                            }
                        });
                    }
                    else {
                        if (js) {
                            eval(js);
                        }
                    }

                    
                });
            }
            catch (e) {
                console.log(e);
            }
        });
    };
}