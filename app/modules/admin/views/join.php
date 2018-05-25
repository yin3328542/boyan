<style>
    .status-color-1{color:#888;text-decoration:line-through;}
</style>
<link rel="stylesheet" href="<?php echo base_url('assets/js/jquery.cxcolor-1.2/css/jquery.cxcolor.css');?>">
<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i> 首页</a>
    </div>
    <h1>加盟申请列表</h1>
</div>

<div class="container-fluid">
    <hr>

    <div id="main-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="cmd-list">
                    <div class="btn-group">
                        <a href="/admin/join" class="btn btn-default  active">加盟申请列表</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th width="30"><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"></th>
                    <th>ID</th>
                    <th>姓名</th>
                    <th>电话</th>
                    <th width="20%">公司名称</th>
                    <th>状态</th>
                    <th width="220">管理</th>
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

<script type="template/javascript" id="data-item">
    <tr class="status-color-<%=is_end%>">
        <td><input type="checkbox" class="J_CheckItem" value="<%=id%>"></td>
        <td><%=id%></td>
        <td><%=name%></td>
        <td><%=mobile%></td>
        <td><%=company%></td>
        <td><%=status_str%></td>
        <td>
            <%if(status==0){%>
            <a href="#" title="标识已联系" class="btn btn-default item-edit"><span class="glyphicon glyphicon-ok"></span></a>
            <%}else{%>
             已联系
            <%}%>
        </td>
    </tr>
</script>

<script type="text/template" id="add-tpl">
    <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
            <label class="col-sm-2 control-label">时间</label>
            <div class="col-sm-6">
                <label class="form-inline">
                    <input type="text" class="form-control" id="date" name="date" value="" placeholder="2017.6">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">主题</label>
            <div class="col-sm-6">
                <label class="form-inline">
                    <input type="text" class="form-control" id="title" name="title" value="">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">内容</label>
            <div class="col-sm-6">
                <label class="form-inline">
                    <textarea name="detail" id="detail" class="form-control" cols="40" rows="4" ></textarea>
                </label>
            </div>
        </div>
    </form>
</script>

<script type="template/javascript" id="edit-tpl">
    <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
            <label class="col-sm-2 control-label">时间</label>
            <div class="col-sm-6">
                <label class="form-inline">
                    <input type="text" class="form-control" id="date" name="date" value="<%=date%>" placeholder="2017.6">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">主题</label>
            <div class="col-sm-6">
                <label class="form-inline">
                    <input type="text" class="form-control" id="title" name="title" value="<%=title%>">
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">内容</label>
            <div class="col-sm-6">
                <label class="form-inline">
                    <textarea name="detail" id="detail" class="form-control" cols="40" rows="4" ><%=detail%></textarea>
                </label>
            </div>
        </div>
    </form>
</script>

