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
            if ($.inArray(src, instance.loadedScripts)===-1) {
                instance.loadedScripts.push(src);
            }
        }
    });

    $('link').each(function (i, linkTag) {
        var src = $(linkTag).attr('src');
        var rel = $(linkTag).attr('rel');
        if (rel === 'stylesheet' && src) {
            if (!$.inArray(src, instance.loadedCssFile)) {
                instance.loadedCssFile.push(src);
            }
        }
    });

    this.init = function (selector) {

        $(selector).on('pjax:end', function (e) {
            try {
                instance.pjaxEndEvent(e);
            }
            catch (e) {
                console.log(e);
            }
        });
    };

    this.loadCssFiles = function (cssFiles) {
        $(cssFiles).each(function (k, cssFile) {
            if ($.inArray(cssFile, instance.loadedCssFile) === -1) {
                instance.loadedCssFile.push(cssFile);
                $("<link/>", {
                    rel: "stylesheet",
                    type: "text/css",
                    href: cssFile
                }).appendTo("head");
            }
        });
    };

    this.getJsFiles = function (positions) {
        var jsFiles = [];
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
        });
        return jsFiles;
    };

    this.getJsScripts = function (positions) {
        var js = '';
        $(Object.keys(positions)).each(function (i, position) {
            if (typeof positions[position].js === "string") {
                js += positions[position].js;
            }
        });
        return js;
    };

    this.pjaxEndEvent = function (e) {
        try {
            var $target = $(e.target);
            $target.find('.js-pjax-scripts').each(function (index, element) {
                var positions = JSON.parse($(element).html());

                if (positions.cssFiles) {
                    instance.loadCssFiles(Object.keys(positions.cssFiles));
                }

                var jsFiles = instance.getJsFiles(positions);
                var js = instance.getJsScripts(positions);

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
//            $target.find('input:first').focus();
            alert($target.find('input[type=text]:first').val());
        }
        catch (e) {
            console.log(e);
        }
    };
}