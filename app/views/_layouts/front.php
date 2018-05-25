<html>
<head>
    <title><?php if($news['title']!=''){?><?php echo($news['title'].'-'.$news['cate_name'].'-')?><?php }?><?php echo($column['title']);?></title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible"content="IE=8">
    <meta http-equiv="X-UA-Compatible"content="IE=7">
    <meta name="keywords" content="<?php if($news['keywords']!=''){?><?php echo($news['keywords'])?><?php }else{?><?php echo $column['keywords'];?><?php }?>"/>
    <meta name="description" content="<?php if($news['head_desc']!=''){?><?php echo($news['head_desc'])?><?php }else{?><?php echo $column['description'];?><?php }?>"/>
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/orange/favicon.ico');?>" type="image/x-icon"/>
    <link rel="icon" href="<?php echo base_url('assets/img/orange/favicon.ico');?>" type="image/x-icon"/>
    <link rel="Bookmark" href="<?php echo base_url('assets/img/orange/favicon.ico');?>" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/orange/common.css');?>">
    <script type="text/javascript" src="<?php echo base_url('assets/js/orange/jquery-2.1.1.js');?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/orange/a.js');?>"></script>

    <script type="text/javascript">
        var _global = <?php echo json_encode($_global);?>;
        //console.log(_global);
        $(document).ready(function(){
            $.MeChat();
            $('.Gfweixin').click(function(){
                $('.Showmodle').show();
                $('.Showmodle img').attr('src','<?php echo base_url('assets/img/orange/weweima1.jpg');?>');

            });
            $('.APPimg').click(function(){
                $('.Showmodle').show();
                $('.Showmodle img').attr('src','<?php echo base_url('assets/img/orange/erweima2.jpg');?>')
            })

            $('.on_chat').click(function(e){
                e.preventDefault();

                $.doMeChat();
                $('.Showmodle').hide();
            })
        })
    </script>
</head>
<body>
<div id="telPhone">
    <a><b>在线客服</b><em></em></a>
    <a><b>拨打电话</b><em></em></a>
    <a><b>官方微信</b><em></em></a>
    <a><b>APP</b><em></em></a>

</div>
<div class="header">
    <ul class="clearfixs">
        <li><a href="welcome">首 页</a></li>
        <li><a href="dian">121店</a></li>
        <li><a href="fenxiangjia">分享家</a></li>
        <li><a href="ruzhu">品牌商入驻</a></li>
        <li><a href="jiameng">服务商加盟</a></li>
    </ul>
</div>

<?php echo $block_content;?>

</body>
</html>

<div class="Showmodle">
    <div></div>
    <img src="images/weweima1.jpg"/>
</div>
<div id="telPhone">
    <a href="javascript:void(0);" class="on_chat"><b>在线客服</b><em></em></a>
    <a href="tel:4000-911-121"><b>拨打电话</b><em></em></a>
    <a class="Gfweixin"><b>官方微信</b><em></em></a>
    <a class="APPimg"><b>APP</b><em></em></a>
</div>