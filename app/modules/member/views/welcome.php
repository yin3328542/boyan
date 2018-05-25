<style>
    .avatar{
        background: url('<?php echo $assets_url; ?>/img/default-user.png') center center no-repeat #666;
        background-size: 80px 80px;
        border-radius: 80px;
    }
</style>
<div id="content-header">
    <div id="breadcrumb">
        <a href="/siter" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i> 首页</a>
        <a href="/siter/member">客户管理</a>
    </div>
    <h1>客户列表</h1>
</div>

<div class="container-fluid">
    <hr>

    <div id="goods-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right">
                    <form class="clearfix" role="search" onsubmit="return false;">
                        <div class="form-group pull-left">
                            <input type="text" name="keyword" class="form-control" placeholder="姓名|昵称|手机号码">
                        </div>
                        <a href="#" class="btn btn-default pull-left" id="btn-search"><span class="glyphicon glyphicon-search"></span></a>
                    </form>
                </div>
                <div class="cmd-list">
                    <div class="btn-group" id="btn-status">
                        <a href="#" rel="all" class="btn btn-default">全部</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th width="30"><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"></th>
                    <th>头像</th>
                    <th>姓名</th>
                    <th>昵称</th>
                    <th>地区</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <footer class="panel-footer clearfix">
                <div id="pagination" class="pull-right"></div>
            </footer>
        </div>
    </div>
</div>

<script type="text/template" id="data-item">
    <tr>
        <td><input type="checkbox" class="J_CheckItem" value="<%=id%>"></td>
        <td> <img class="avatar" src="<%=avatar%>" width="50"> </td>
        <td><%=name%></td>
        <td><%=nickname%></td>
        <td><%=province%>-<%=city%></td>
        <td>
           <% if(in_blacklist ==0){%>
          <a href="javascript:" title="加入黑名单" class="btn btn-default item-in"><span class="glyphicon glyphicon-eye-close"></span></a>
          <% }else{ %>
            <a href="javascript:" title="移出黑名单" class="btn btn-default item-out"><span class="glyphicon glyphicon-eye-open"></span></a>
           <% }%>
        </td>
    </tr>
</script>

<script type="text/template" id="send_msg-tpl">
    <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
            <label class="col-sm-3 control-label tuihuo-ps"> 消息标题：</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" style="width: 200px;" name="title" >
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label tuihuo-ps"> 消息内容：</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="content"></textarea>
            </div>
        </div>
    </form>
</script>

<script type="text/template" id="pay-tpl">
    <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
            <label class="col-sm-3 control-label tuihuo-ps"> 付款金额：</label>
            <div class="col-sm-9">
                <label><input type="text" class="form-control" style="width: 200px;" name="amount" >元</label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label tuihuo-ps"> 备注：</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="remark"></textarea>
            </div>
        </div>
    </form>
</script>

<script type="text/template" id="view-site-tpl">
    <iframe src ="http://<%=site_id%>.ugc.zuanla.com.cn/?open_id=<%=open_id%>" frameborder="0" scrolling="auto" width="320px" height="480px">
        <p>Your browser does not support iframes.</p>
    </iframe>
</script>