<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
        <a href="/admin/admin" class="tip-bottom" data-original-title="账号管理">账号管理</a>
    </div>
    <h1>账号列表</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="main-view">
        <div class="panel panel-default">
            <div class="panel-heading">

                <div class="cmd-list">
                    <div class="btn-group">
                        <a id="siter-add-tab" href="#" class="btn btn-default">添加账号</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th>姓名</th>
                    <th>登录账号</th>
                    <th>最后登录IP</th>
                    <th>最后登录时间</th>
                    <th>创建时间</th>
                    <th>角色</th>
                    <th>状态</th>
                    <th width="160">管理</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="template/javascript" id="data-item">
    <tr>
        <td><%=real_name%></td>
        <td><%=username%></td>
        <td><%=last_ip%></td>
        <td><%=dt_login%></td>
        <td><%=dt_add%></td>
        <td><%=role_name%></td>
        <td><%if(status == 1){%>已启用<%} else {%>已停用<%}%></td>
        <td>
        <%if(role_id>0){%>
            <!--<%if(status == 1){%><a href="#" class="btn btn-default item-disabled"><span class="glyphicon glyphicon-minus-sign"></span> 停用</a><%}%>
            <%if(status != 1){%><a href="#" class="btn btn-default item-enabled"><span class="glyphicon glyphicon-ok-sign"></span> 启用</a><%}%>-->
            <a href="#" class="btn btn-default item-remove"><span class="glyphicon glyphicon-trash"></span> 删除</a>
        <%}%>
         </td>
    </tr>
</script>

<script type="template/javascript" id="add-tpl">
    <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span> 所属角色</label>
            <div class="col-sm-9">
                <select name="role_id" class="form-control">
                    <option value="">选择角色</option>
                    <%for(var k in roles){%>
                    <%if(roles[k].id > 0) {%>
                    <option value="<%=roles[k].id%>"><%=roles[k].role_name%></option>
                    <%}%>
                    <%}%>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>姓名</label>
            <div class="col-sm-9">
                <input type="text" name="real_name" class="form-control" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>登录账号</label>
            <div class="col-sm-9">
                <input type="text" name="username" class="form-control" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>登录密码</label>
            <div class="col-sm-9">
                <input type="text" name="password" class="form-control" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>确认密码</label>
            <div class="col-sm-9">
                <input type="text" name="confirm_password" class="form-control" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">邮箱</label>
            <div class="col-sm-9">
                <input type="text" name="email" class="form-control" value="">
            </div>
        </div>
    </form>
</script>