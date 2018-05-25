;(function($){
    $.extend({
        // 内容
        addField: function(options) {
            var items = $('.app-fields .app-field');
            if (items.size()) {
                items.removeClass('editing');
            }
            var opts = $.extend({}, {
                top: 0,
                fieldName: '',
                fieldContent: '',
                callFn: null
            }, options);
            var str = '';
            str += '<div class="app-field editing" data-field-name="' + opts.fieldName + '" style="top:' + opts.top + 'px"><div class="control-group">';
            str += opts.fieldContent;
            str += '</div><div class="control-actions"><div class="actions-wrap"><span class="action delete">删除</span></div></div><div class="control-sort"><span class="ion kr-navicon"></span></div></div>';
            $('.app-fields').append(str);
            if (typeof opts.callFn === 'function') {
                opts.callFn();
            }
        },
        // 选项
        addModule: function(options) {
            var opts = $.extend({}, {
                top: 100,
                moduleName: 'nav_text',
                moduleContent: '<section class="selects">谢谢使用我的店铺我做主软件</select>',
                callFn: null
            }, options);
            var str = '';
            str += '<section class="app-module-select" data-module-name="' + opts.moduleName + '" style="top:' + opts.top + 'px">';
            str += opts.moduleContent;
            str += '</select>';
            if ($('.app-module-select').size()) {
                $('.app-module-select').remove();
            }
            $('.app-wrap').append(str);
            $('.app-module-select[data-module-name=' + opts.moduleName + ']').fadeIn();
            if (typeof opts.callFn === 'function') {
                opts.callFn();
            }
        },
        removeModule: function() {
            $('.app-module-select').remove();
        },
        screenResize: function(fn) {
            fn();
            $(window).resize(function() {
                fn();
            });
        }
    });

    $.fn.extend({
        TranslateVal: function() {
            if ($(this).size() > 1) return false;
            var matrix = $(this).css('-webkit-transform').split(',');
            if (matrix[0].indexOf('matrix3d') > -1) {
                var val = {
                    x: parseFloat(matrix[12]),
                    y: parseFloat(matrix[13]),
                    z: parseFloat(matrix[14])
                }
            } else {
                var val = {
                    x: parseFloat(matrix[4]),
                    y: parseFloat(matrix[5]),
                    z: 0
                }
            }
            return val;
        },
        TranslateMove: function(x, y, z, time, callback) {
            return $(this).each(function() {
                var here = this;
                $(this).css({
                    '-webkit-transform': 'translate3d(' + x + 'px, ' + y + 'px, ' + z + 'px)',
                    '-webkit-transition-duration': time + 's'
                })
                setTimeout(function() {
                    typeof callback === 'function' ? callback.call(here) : null;
                }, time * 1000);
            });
        },
        Touch: function(opts) {
            return $(this).each(function() {
                var def = {
                    start: function() {},
                    move: function() {},
                    end: function() {}
                }
                var opt = $.extend(def, opts);

                var startX = startY = absX = absY = 0;
                $(this).on({
                    'touchstart': function(e) {
                        var E = e.originalEvent;
                        absX = absY = 0;
                        startX = E.touches[0].pageX,
                            startY = E.touches[0].pageY;
                        var re = {
                            e: e,
                            x: absX,
                            y: absY
                        }
                        opt.start.call(this, re);
                    },
                    'touchmove': function(e) {
                        var E = e.originalEvent;
                        var nowX = E.touches[0].pageX,
                            nowY = E.touches[0].pageY;
                        absX += (nowX - startX),
                            absY += (nowY - startY);
                        startX = nowX;
                        startY = nowY;
                        var re = {
                            e: e,
                            x: absX,
                            y: absY
                        }
                        opt.move.call(this, re);
                    },
                    'touchend': function() {
                        var re = {
                            e: null,
                            x: absX,
                            y: absY
                        }
                        opt.end.call(this, re);
                    }
                })
            })
        },
        BannerSwipe: function() {
            return $(this).each(function() {
                var w = $(this).width(),
                    $wrap = $(this).find('.swipe-banner-wrap'),
                    $list = $wrap.find('.swipe-list'),
                    $ul = $list.find('ul'),
                    $li = $ul.find('li'),
                    len = $li.length,
                    ul_w = w * len,
                    max = w * (len + 1),
                    $ind = $wrap.find('.swipe-ind');
                $li.width(w);
                var first = $li.eq(0).clone(),
                    last = $li.eq(len - 1).clone();
                $ul.width((len + 2) * w);
                $ul.TranslateMove(-w, 0, 0, 0).prepend(last).append(first);
                $li = $ul.find('li');
                $li.eq(1).addClass('active');

                var str = '';
                for (var i = 0; i < len; i++) {
                    str += '<div class="swipe-ind-item"></div>';
                }
                $ind.html(str);
                var $ind_item = $ind.find('.swipe-ind-item');


                function setInd() {
                    var val = $ul.TranslateVal().x;
                    var ind = Math.abs(val) / w;
                    if (Math.floor(ind) != ind) {
                        ind = Math.floor(ind);
                    }
                    $li.eq(ind).addClass('active').siblings().removeClass('active');

                    if (ind == 0) {
                        ind = len - 1;
                    }
                    if (ind == (len + 1)) {
                        ind = 1;
                    }
                    $ind_item.eq(ind - 1).addClass('active').siblings().removeClass('active');
                    $ul.removeClass('moving');
                }

                var t = null;

                function auto() {
                    var move = -w;
                    t = setInterval(function() {
                        move = move - w;
                        $ul.addClass('moving');
                        $ul.TranslateMove(move, 0, 0, 0.3, function() {
                            if (move == -max) {
                                move = -w;
                                $(this).TranslateMove(move, 0, 0, 0);
                            }
                            setInd();
                        })
                    }, 2000);
                }

                auto();

                var s = 0;
                $ul.Touch({
                    start: function(re) {
                        if (!$ul.hasClass('moving')) {
                            s = $(this).TranslateVal().x;
                        }
                    },
                    move: function(re) {
                        if (!$ul.hasClass('moving')) {
                            if (Math.abs(re.x) > 5) {
                                re.e.preventDefault();
                                if (t && Math.abs(re.x) > 10) {
                                    clearInterval(t);
                                }
                            }
                            $(this).TranslateMove(s + re.x, 0, 0, 0);
                        }
                    },
                    end: function(re) {
                        if (!$ul.hasClass('moving')) {
                            var move = s;
                            if (Math.abs(re.x) < 50) {
                                $(this).TranslateMove(move, 0, 0, 0.3, function() {
                                    $ul.removeClass('moving');
                                });
                            } else {
                                $ul.addClass('moving');
                                if (re.x < -50) {
                                    move = move - w;
                                }
                                if (re.x > 50) {
                                    move = move + w;
                                }
                                $(this).TranslateMove(move, 0, 0, 0.3, function() {
                                    if (move == -max) {
                                        $(this).TranslateMove(-w, 0, 0, 0);
                                    }
                                    if (move == 0) {
                                        $(this).TranslateMove(-(max - w), 0, 0, 0);
                                    }
                                    setInd();
                                });
                            }
                        }
                    }
                })
            })
        }
    });
})(jQuery);
