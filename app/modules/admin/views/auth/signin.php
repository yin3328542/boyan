<!DOCTYPE html>
<html>
<head>
    <title>总后台管理</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="kunrou.INC">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/app.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('app/modules/admin/assets/css/'.$style_file.'.css');?>">
    <script type="text/javascript">
        var _global = <?php echo json_encode($_global);?>;
    </script>
    <script src="<?php echo base_url('assets/js/require-config.js') ?>"></script>
    <script data-main="<?php echo $app_assets_path;?>js/<?php echo $js_file;?>" src="<?php echo base_url('assets/js/require/require.js') ?>"></script>
</head>
<body>
    <div class="container">
        <form class="form-signin">
            <h3 class="form-signin-heading text-center">管理登录</h3>
            <div class="control-group">
                <input type="text" class="form-control" placeholder="账号" name="username" id="username" datatype="*" data-toggle="tooltip" data-placement="right" data-trigger="manual" title="请输入用户名" required autofocus>
            </div>
            <div class="control-group">
                <input type="password" class="form-control" placeholder="密码" name="password" id="password" datatype="*" data-toggle="tooltip" data-placement="right" data-trigger="manual" title="请填写密码" required>
            </div>
            <div id="msg-container" class="alert alert-danger hide"></div>
            <button id="btn_signin" type="submit" class="btn btn-success btn-block btn-lg" data-loading-text="正在登陆...">点击登录</button>
        </form>

    </div>
</body>
</html>