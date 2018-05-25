<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>轮播图列表</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="banner-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="cmd-list">
                    <div class="btn-group">
                        <?php foreach ($banner_config_list as $k=>$v){?>
                            <a  href="/admin/banner/<?php echo $v['type']?>" class="btn btn-default <?php if($type==$v['type']){?>active<?php }?>" ><?php echo $v['name']?>轮播</a>
                        <?php }?>
                        <a id="coupon-add-tab" href="/admin/banner/add/<?php echo $type;?>" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>添加</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th nowrap>图片</th>
                    <th nowrap>名称</th>
                    <th nowrap>排序</th>
                    <th nowrap>显示</th>
                    <th nowrap>时间</th>
                    <th nowrap width="200">操作</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="template/javascript" id="data-item">
    <tr id="banner_<%=id%>">
        <td><img src="<%=img_file%>" width="60"/></td>
        <td><%=name%></td>
        <td><input type="text" name="banner-list-<%=id%>" class="form-control banner-list banner-list-<%=id%>" style="width:50px;" maxlength="3" value="<%=listorder%>"/></td>
        <td>
            <%if(status == 1) {%>
                <a href="#" class="btn btn-default status-display-none-<%=id%>" title="点击隐藏"><span class="glyphicon glyphicon-ok"></a>
            <%} else {%>
                <a href="#" class="btn btn-default status-display-block-<%=id%>" title="点击显示"><span class="glyphicon glyphicon-remove"></a>
            <%}%>
        </td>
        <td><%=ad_time%></td>
        <td>
            <a href="javascript:void(0);" class="btn btn-sm btn-default item-remove-<%=id%>"><span class="glyphicon glyphicon-trash"></span></a>
            <a href="/admin/banner/edit/<%=id%>" title="编辑" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>
        </td>
    </tr>
</script>