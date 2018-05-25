/**
 * Created by river on 14-10-23.
 */
require.config(require_config);

define([
    'jquery',
    'components/kr-common'
], function( $, kunrou ){

    var model = function() {
        var md = {
            //更新数据
            save : function() {
                if(this.valid_config) {
                    if(!this.validation()) {
                        return false;
                    }
                }
                var _this = this;
                var ajax_type = 'post';
                var ret = true;
                if(typeof(_this.data['id']) !== 'undefined') {
                    ajax_type = typeof(_this.ajax_type) === 'undefined' ? 'put' : _this.ajax_type;
                }
                console.log('ajax_data', _this.data);
                $.ajax({
                    'url' : _this.url,
                    'type' : ajax_type,
                    'async' : false,
                    'data' : _this.data,
                    'success' : function(result) {
                        if(typeof(result.ret) === 'undefined') {
                            _this.error('通讯错误');
                            ret = false;
                        }
                        if(result.ret == 0) {
                            $(_this.id).html('');
                            _this.success();
                        } else {
                            _this.error(result.msg);
                            ret = false;
                        }
                    }, 'error' : function() {
                        _this.error('通讯错误');
                        ret = false;
                    }
                });
                return ret;
            },

            //获取信息
            fetch : function() {
                var _this = this;
                var ret = true; //请求是否成功
                $.ajax({
                    'url' : _this.url,
                    'type' : 'get',
                    'async' : false,
                    'data' : {id: _this.data.id},
                    'success' : function(result) {
                        if(typeof(result.ret) === 'undefined') {
                            _this.error('通讯错误');
                            ret = false;
                        }
                        if(result.ret == 0) {
                            if(typeof(result.data) == 'object') {
                                _this.data = result.data;
                            }
                            _this.render();
                        } else {
                            _this.error(result.msg);
                            ret = false;
                        }
                    }, 'error' : function() {
                        _this.error('通讯错误');
                        ret = false;
                    }
                });
                return ret;
            },

            //删除
            del: function() {
                var _this = this;
                var ret = true; //请求是否成功
                $.ajax({
                    'url' : _this.url,
                    'type' : 'delete',
                    'async' : false,
                    'data' : {id:this.data.id, menu_type:this.data.menu_type},
                    'success' : function(result) {
                        if(typeof(result.ret) === 'undefined') {
                            _this.error('通讯错误');
                            ret = false;
                        }
                        if(result.ret == 0) {
                            _this.success();
                        } else {
                            _this.error(result.msg);
                            ret = false;
                        }
                    }, 'error' : function() {
                        _this.error('通讯错误');
                        ret = false;
                    }
                });
                return ret;
            },

            render: function() {

            },

            //数据
            data : {},

            success : function() {
                kunrou.alert({msg:'操作成功'});
            },
            error : function(msg) {
                kunrou.alert({msg:msg,type: 'error'});
            },

            valid_config : {

            },

            //格式验证
            validation : function() {
                var _this = this;
                var valid_format = {
                    email : function(item) {
                        var Reg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
                        if( Reg.test(item) ){
                            return true;
                        }
                        return false;
                    },
                    number : function(item) {
                        var Reg = /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/;
                        if( Reg.test(item) ){
                            return true;
                        }
                        return false;
                    },
                    required : function(item) {
                        if( item == '' || item === undefined || item === null ){
                            console.log(item);
                            return false;
                        }
                        console.log(item);
                        return true;
                    }
                    //TODO 验证待完善...
                };
                var valid_message = {
                    email : '邮箱地址格式不正确',
                    number : '请输入正确的值',
                    required : '这里不能留空哦'
                };

                var valid_error = function(dom, item) {
                    $('input[name="'+dom+'"]').addClass('invalid');
                    //$('input[name="'+dom+'"]').parent().append('<span class="err">'+valid_message[item]+'</span>');
                };

                if(this.valid_config) {
                    var valid = true;
                    $.each(this.valid_config, function(index, item) {
                        if(typeof(_this.data[index]) !== 'undefined') {
                            if(typeof(item) !== undefined) {
                                if(!valid_format[item](_this.data[index])) {
                                    valid = false;
                                    valid_error(index, item);//输出错误信息
                                }
                            }
                        }
                    })
                    return valid;
                }
                return true;
            },

            extend : function(obj) {
                var _this = this;
                $.each(obj, function(index,item) {
                    _this[index] = item;
                });
                return _this;
            }
        };
        return md;
    };
    return model;
});