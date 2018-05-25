<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>发布消息</h1>
</div>

<div class="container-fluid">
    <hr>

    <div id="goods-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="cmd-list">
                    <div class="btn-group" id="btn-status">
                        <a href="/admin/message_list" class="btn btn-default active">消息列表</a>
                        <a href="/admin/message" class="btn btn-default">发布消息</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th width="30"><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"></th>
                    <th>标题</th>
                    <th>类型</th>
                    <th>发送人id</th>
                    <th>接收人id</th>
                    <th>时间</th>
                    <th width="160">操作</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <footer class="panel-footer clearfix">
                <!--<div class="form-inline pull-left">
                    <div class="checkbox"><label><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"> 全选/反选</label></div>
                    <button type="button" id="J_BatchDeleteBtn" class="btn btn-default ml15" data-loading-text="删除中...">删除</button>
                </div>-->
                <div id="pagination" class="pull-right"></div>
            </footer>
        </div>
    </div>
</div>

<script type="text/template" id="data-item">
    <tr>
        <td><input type="checkbox" class="J_CheckItem" value="<%=id%>"></td>
        <td title="<%=desc%>"><%=title%></td>
        <td><%=type_str%></td>
        <td><%=send_id%></td>
        <td><%=receive_id%></td>
        <td><%=dt_add_str%></td>
        <td>
            <a href="#" class="btn btn-default item-hide" title="删除"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
    </tr>
</script>

