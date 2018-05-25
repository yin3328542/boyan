/**
 * Created by river on 14-3-14.
 */
define([
    'jquery',
    'bootstrap',
    'components/kr-common',
], function( $, bootstrap, KR){


    //全选反选
    $(document).on('click', '[data-toggle="chackall"]', function () {
        var $target = $($(this).attr('data-target'));
        $target.prop('checked', this.checked);
        $('[data-toggle="chackall"]').prop('checked', this.checked);
    });

    //AJAX签名
    $(document).ajaxSend(function(evt, request, settings){
        request.setRequestHeader('App-Key', window._global.app_key);
        if (window._global.access_token != '') {
            request.setRequestHeader('Access-Token', window._global.access_token);
        }
    });

    $(document).ajaxError(
        function(e,request) {
            //console.log(request);
            response = request.responseJSON;
            if (request.status == 403) {
                window.location.href = window._global.url.signout;
            }
        }
    );

    $(document).ready(function(){



        // === Sidebar navigation === //

        $('.submenu > a').click(function(e)
        {
            e.preventDefault();
            var submenu = $(this).siblings('ul');
            var li = $(this).parents('li');
            var submenus = $('#sidebar li.submenu ul');
            var submenus_parents = $('#sidebar li.submenu');
            if(li.hasClass('open'))
            {
                if(($(window).width() > 768) || ($(window).width() < 479)) {
                    submenu.slideUp();
                } else {
                    submenu.fadeOut(250);
                }
                li.removeClass('open');
            } else
            {
                if(($(window).width() > 768) || ($(window).width() < 479)) {
                    submenus.slideUp();
                    submenu.slideDown();
                } else {
                    submenus.fadeOut(250);
                    submenu.fadeIn(250);
                }
                submenus_parents.removeClass('open');
                li.addClass('open');
            }
        });

        var ul = $('#sidebar > ul');

        $('#sidebar > a').click(function(e)
        {
            e.preventDefault();
            var sidebar = $('#sidebar');
            if(sidebar.hasClass('open'))
            {
                sidebar.removeClass('open');
                ul.slideUp(250);
            } else
            {
                sidebar.addClass('open');
                ul.slideDown(250);
            }
        });

        // === Resize window related === //
        $(window).resize(function()
        {
            if($(window).width() > 479)
            {
                ul.css({'display':'block'});
                $('#content-header .btn-group').css({width:'auto'});
            }
            if($(window).width() < 479)
            {
                ul.css({'display':'none'});
                fix_position();
            }
            if($(window).width() > 768)
            {
                $('#user-nav > ul').css({width:'auto',margin:'0'});
                $('#content-header .btn-group').css({width:'auto'});
            }
        });

        if($(window).width() < 468)
        {
            ul.css({'display':'none'});
            fix_position();
        }

        if($(window).width() > 479)
        {
            $('#content-header .btn-group').css({width:'auto'});
            ul.css({'display':'block'});
        }



        // === Fixes the position of buttons group in content header and top user navigation === //
        function fix_position()
        {
            var uwidth = $('#user-nav > ul').width();
            $('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});

            var cwidth = $('#content-header .btn-group').width();
            $('#content-header .btn-group').css({width:cwidth,'margin-left':'-' + uwidth / 2 + 'px'});
        }

    });

    (function($) {
        //tab
        $.fn.kr_tab = function() {
            $(this).on('click', 'a', function(e) {
                 e.preventDefault();
                 $(this).parent().addClass('active').siblings().removeClass('active');
                 $($(this).attr('href')).fadeIn().siblings().hide();
            });
            var first = $(this).find('a:first');
            first.parent().addClass('active');
            $(first.attr('href')).show().siblings().hide();
        };
    })(jQuery);

    //数组删除方法
    Array.prototype.remove = function(index) {
        if (index > -1) {
            this.splice(index, 1);
        }
    };

    //in_array
    String.prototype.in_array = function(array){
        for(var i in array){
            if(array[i]==this){
                return true;
            }
        }
        return false;
    };

    return KR;

});