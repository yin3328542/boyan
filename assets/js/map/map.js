/*
* 地图位置选择器
*
* 依赖脚本：
* 1.jQuery
* 2.百度地图API(已经自动引用)
*     <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.5&ak=D5894ef9e6eb52db5afab2366bbbef58"></script>
*
* DOM:
* <input type="text" class="weiba-control-address" value="人民路" weiba-map-city="成都" weiba-map-lng="" weiba-map-lat="">
*
*
* */
//1.引用百度地图
//document.write('<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=D5894ef9e6eb52db5afab2366bbbef58"></script>');

//2.逻辑
define('map', ['jquery'], function($){
    var map = function(){
        var mapid = 'baidumap' + ((Math.random()*100000)>>0);
        var html = '<div class="weiba-map-box" style="display:none;left:0;position:absolute;width:500px;height:450px;z-index:999">';
        html += '<div class="weiba-map-control-box" style="font-size: 18px;color:#FFFFFF;text-align:right;position: absolute;top:0;left:0;width:100%;height: 32px;line-height: 32px;background: #495553;">';
        html += '<span class="weiba-map-control-ok map-ok" style="cursor:pointer;margin:0 12px;">确定</span><span class="weiba-map-control-cancel map-cancel" style="cursor:pointer;margin:0 12px;">取消</span>';
        html +='</div>';
        html += '<div class="weiba-map-body" id="' + mapid + '" style="position:absolute;left:0;top:32px;width:100%;height:418px;"></div></div>';

        var $elm_input,new_lng, new_lat;

        var $baidu_map = $(html)
            .appendTo('body').on('click','.map-cancel',function(){
                $baidu_map.fadeOut();
            }).on('click','.map-ok',function(){
                $elm_input.attr('weiba-map-lng',new_lng);
                $elm_input.attr('weiba-map-lat',new_lat);
                $baidu_map.fadeOut();
                var _position = new_lng + "," + new_lat;
                var _html = '<img src="http://api.map.baidu.com/staticimage?center=' + _position + '&markers=' + _position + '">';
                $("#map").html(_html);
            });

        //初始化百度地图
        var map = new BMap.Map(mapid);
        var markerIcon = new BMap.Icon('/assets/img/marker.png', new BMap.Size(39, 25), {
            'anchor': new BMap.Size(20, 12)
        });

        map.addControl(new BMap.NavigationControl());
        map.addControl(new BMap.ScaleControl());
        map.addControl(new BMap.OverviewMapControl());
        map.enableScrollWheelZoom();
        map.addControl(new BMap.MapTypeControl());

        //获取当前所在城市
        var localCity = new BMap.LocalCity();
        var localCityName = '北京';
        localCity.get(function (r) {
            localCityName =  r.name;
            map.centerAndZoom(r.center, r.level);
        });

        $('body').on('keyup', '.weiba-control-address',function(){
            var $this = $(this),val = $.trim($this.val());
            /*        $this.attr('weiba-map-lat','');
             $this.attr('weiba-map-lng','');*/
            if(val){
                showMapControl($this);
            }else{
                if($this.data('mapcontrol')){
                    $this.data('mapcontrol').remove();
                    $this.data('mapcontrol','');
                }
            }
        });

        $('.weiba-control-address').each(function(){
            showMapControl($(this))
        });

        function showMapControl($elm){
            if(!$elm.data('mapcontrol')){
                //var $control = $('<a href="javascript:;">地图定位</a>').on('click',function(){
                var $control = $('<a href="javascript:;">自动定位</a>').on('click',function(){
                    showBaiDuMap({
                        $elm : $elm,
                        address : $elm.val(),
                        lng : $elm.attr('weiba-map-lng'),
                        lat : $elm.attr('weiba-map-lat'),
                        city : $elm.attr('weiba-map-city')
                    });
                });
                $elm.data('mapcontrol',$control);
                $elm.after($control);
            }
        }

        function showBaiDuMap(config){
            map.clearOverlays();
            $elm_input = config['$elm'];
            new_lng = config['lng'];
            new_lat = config['lat'];

            var $elm = config['$elm'];
            if($elm){
                var offset = $elm.offset();
                $baidu_map.css({
                    'left' : offset.left,
                    'top' : (offset.top + $elm.outerHeight())
                });
            }

            var lat = config['lat'],lng = config['lng'],address = config['address'],city = config['city'];

            if(!city){
                city = localCityName;
            }

            if(lat && lng){//存在经纬度显示标注点
                addMarker({
                    lng : lng,
                    lat : lat,
                    label : address
                },function(point){
                    new_lng = point['lng'];
                    new_lat = point['lat'];
                });

                new_lng = lng;
                new_lat = lat;
                $baidu_map.fadeIn(function(){
                    map.centerAndZoom(new BMap.Point(lng*1,lat*1),18);
                });

            }else{//不存在则根据地址进行坐标解析
                var myGeo = new BMap.Geocoder();
                myGeo.getPoint(address, function (point) {
                    if (point) {

                        addMarker({
                            lng : point['lng'],
                            lat : point['lat'],
                            label : address
                        },function(point){
                            new_lng = point['lng'];
                            new_lat = point['lat'];
                        });

                        new_lng = point['lng'];
                        new_lat = point['lat'];
                        $baidu_map.fadeIn(function(){
                            map.centerAndZoom(point,18);
                        });

                    }else{
                        alert('无法定位你提供的地址，请重新输入再打开地图定位。');
                    }
                }, city);
            }
        }

        //4.添加标注点
        function addMarker(data,callback) {
            if (map && map.addOverlay) {
                /*
                 var marker = new BMap.Marker(new BMap.Point(data['lng'], data['lat'], {
                 'raiseOnDrag': true,
                 'enableDragging' : true,
                 'icon': markerIcon,
                 'title' : '拖动图标编辑精确位置'
                 }));
                 */
                var marker = new BMap.Marker(new BMap.Point(data['lng'], data['lat']));

                var label = new BMap.Label(data['label'], { 'offset': new BMap.Size(-2, 26) });

                marker.setLabel(label);

                marker.addEventListener('dragend', function (e) {
                    var point = e.point;
                    if(callback){
                        callback.call(this,point);
                    }
                    map.panTo(point);
                });
                map.addOverlay(marker);
                //marker.setAnimation(BMAP_ANIMATION_BOUNCE);
                marker.enableDragging();
                marker.setTitle('拖动以便调整精确位置')
            }
        }
    };
    return map;
});