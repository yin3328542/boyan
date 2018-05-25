/**
 * Created by river on 14-10-23.
 */
require.config(require_config);

define([
    'jquery'
], function( $ ){

    var collection = function() {
        var ct = {
            //获取数据
            fetch : function() {
                var _this = this;
                $.ajax({
                    'url' : _this.url,
                    'type' : 'get',
                    'async' : true,
                    'data' : _this.options,
                    'success' : function(result) {
                        if(typeof(result.ret) !== 'undefined' && result.ret == 0) {
                            _this.result = result;
                            _this.data = result.data;
                            _this._count = result._count;
                            $(_this.id).html('');
                            _this.sync();
                        }
                    }, 'error' : function() {
                        _this.data = {};
                        _this._count = 0;
                    }
                });
            },

            page_dom: 'pagination',

            sync : function() {
                console.log('sync');
            },

            render : function() {
                console.log('没有重写输出过程');
            },

            //分页配置
            page : {page_max: 10, page_size: 10, current_page : 1},

            /**
             * NB的分页
             * @returns {boolean}
             */
            paginated : function() {
                var _this = this;
                var html = '<ul class="pagination">';
                html += '<li><a href="javascript:" class="prev_page">&laquo;</a></li>';
                var page_size = this.options.limit ? this.options.limit : this.page.page_size;
                this.page.page_size = page_size;
                //alert(this._count + '--' + page_size);
                if(this._count >= 0) {
                    this.page.page_total = Math.ceil(ct._count / page_size);
                    if(this.page.page_total <= this.page.page_max) {
                        for(var i = 1; i <= this.page.page_total; i ++) {
                            if(i == this.page.current_page) {
                                html += '<li class="active"><a href="javascript:">'+i+'</a></li>';
                            } else {
                                html += '<li><a href="javascript:" class="page" rel="'+i+'">'+i+'</a></li>';
                            }
                        }
                    } else {
                        var page_html = '';
                        var half = this.page.page_max / 2;
                        console.log('half',half);
                        for(var t = 1; t < half; t ++) {
                            console.log('t', t);
                            if((this.page.current_page + t) > this.page.page_total) {
                                break;
                            }
                            page_html += '<li><a href="javascript:" class="page" rel="'+(this.page.current_page + t)+'">'+(this.page.current_page + t)+'</a></li>';
                        }
                        page_html = '<li class="active"><a href="javascript:" >'+this.page.current_page+'</a></li>' + page_html;
                        //var n = this.page.current_page - half - (half- i);
                        for(var l = 1; l < half ; l ++) {
                            console.log('l', l);
                            if((this.page.current_page - l) < 1) {
                                break;
                            }
                            page_html = '<li><a href="javascript:" class="page" rel="'+(this.page.current_page - l)+'">'+(this.page.current_page - l)+'</a></li>' + page_html;
                        }
                        if(t < half) {
                            for(var i = 1; i <= half - t ; i ++) {
                                if(typeof(l) == 'undefined') {
                                    l = 0;
                                }
                                page_html = '<li><a href="javascript:" class="page" rel="'+(this.page.current_page - l - i)+'">'+(this.page.current_page - l - i)+'</a></li>' + page_html;
                            }
                            page_html = '<li><a href="javascript:" class="page" rel="1">1..</a></li>' + page_html;
                        }
                        else if(l < half) {
                            for(var j = 1; j <= half - l ; j ++) {
                                if(typeof(t) == 'undefined') {
                                    t = 0;
                                }
                                page_html = page_html + '<li><a href="javascript:" class="page" rel="'+(this.page.current_page + t + j)+'">'+(this.page.current_page + t + j)+'</a></li>';
                            }
                            page_html = page_html + '<li><a href="javascript:" class="page" rel="'+this.page.page_total+'">...'+this.page.page_total+'</a></li>';
                        }
                        else {
                            page_html = '<li><a href="javascript:" class="page" rel="'+1+'">1...</a></li>' + page_html;
                            page_html = page_html + '<li><a href="javascript:" class="page" rel="'+this.page.page_total+'">...'+this.page.page_total+'</a></li>';
                        }
                        html += page_html;
                    }
                } else {
                    return false;
                }
                html += '<li><a href="javascript:" class="next_page">&raquo;</a></li><li><a href="javascript:">共 '+this.page.page_total+' 页 , '+this._count+' 条记录</a></li>';

                if(this.page.page_total <= 1){
                    html = '<ul class="pagination"><li><a href="javascript:">共 '+this.page.page_total+' 页 , '+this._count+' 条记录</a></li></ul>';
                }
                $('#'+this.page_dom).html(html);
                //绑定事件
                $('.page').on('click', function() {
                    _this.goto(parseInt($(this).attr('rel')));
                });
                $('.prev_page').on('click', function() {
                    _this.goto_prev();
                });
                $('.next_page').on('click', function() {
                    _this.goto_next();
                });
            },

            goto: function(page) {
                this.options.offset = ((page - 1) * this.page.page_size);
                this.page.current_page = page;
                this.fetch();
                console.log(this.page.current_page, 'goto');
            },

            goto_prev: function() {
                if(this.page.current_page > 1) {
                    this.page.current_page --;
                    this.options.offset = ((this.page.current_page - 1) * this.page.page_size);
                    this.fetch();
                }
                console.log(this.page.current_page, 'prev');
            },

            goto_next: function() {
                if(this.page.current_page < this.page.page_total) {
                    this.page.current_page ++;
                    this.options.offset = ((this.page.current_page - 1) * this.page.page_size);
                    this.fetch();
                }
                console.log(this.page.current_page, 'next');
            },

            extend : function(obj) {
                var _this = this;
                $.each(obj, function(index,item) {
                    _this[index] = item;
                });
                return _this;
            }
        };
        return ct;
    };
    return collection;
});
