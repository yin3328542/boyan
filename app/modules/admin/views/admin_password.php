<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>密码修改</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="main-view" >

        <!--设置表单-->
        <div class="panel panel-default">

            <div class="panel-body">
                <form class="form-horizontal" role="form" method="post">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">旧密码：</label>
                        <div class="col-sm-6">

                            <input type="password" class="form-control" style="width: 200px;" name="old_pwd" >

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">新密码：</label>
                        <div class="col-sm-6">

                            <input type="password" class="form-control" style="width: 200px;" name="new_pwd" >

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">确认密码：</label>
                        <div class="col-sm-6">

                            <input type="password" class="form-control" style="width: 200px;" name="pwd" >

                        </div>
                    </div>


                </form>
            </div>

            <footer class="panel-footer">
                <button type="button" class="btn btn-primary ml15" id='btn_save' data-loading-text="修改中...">修改</button>
                <span class="font-red ml10"></span>
            </footer>
        </div>
    </div>
</div>