<!doctype html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=-devicewidth, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
   <title></title>
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/css/bootstrap.min.css">
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/css/app.css">
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/css/sweetalert.css">
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/css/bootstrap-select.css">
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/lib/colorpicker/css/bootstrap-colorpicker.css">
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/css/css.css">
   <link rel="stylesheet" href="{__FRAME_PATH}new_admin/css/iconfont-1.4.css" />
   <link rel="stylesheet" href="//at.alicdn.com/t/font_2620349_xs1y4m37gvq.css" />
   <script type="text/javascript" src="{__FRAME_PATH}new_admin/js/jquery-1.11.3.min.js"></script>
   <script type="text/javascript" src="{__FRAME_PATH}new_admin/js/bootstrap.min.js"></script>
   <script type="text/javascript" src="{__FRAME_PATH}new_admin/js/sweetalert.min.js"></script>
   <script type="text/javascript" src="{__FRAME_PATH}new_admin/js/bootstrap.select.min.js"></script>
   <script src="{__FRAME_PATH}new_admin/js/echarts.common.min.js"></script>
   <script src="{__FRAME_PATH}new_admin/js/bootstrap-datetimepicker.js"></script>
   <script src="{__FRAME_PATH}new_admin/lib/colorpicker/js/bootstrap-colorpicker.js"></script>
   <script src="{__FRAME_PATH}new_admin/js/bootstrap-datetimepicker.zh-CN.js"></script>
   <script src="{__FRAME_PATH}new_admin/js/jquery.jqprint-0.3.js"></script>
   <script src="{__FRAME_PATH}new_admin/js/jquery-migrate-1.2.1.min.js"></script>
   <script src="{__FRAME_PATH}new_admin/js/macarons.js"></script>
   <style>
      .relative{position: relative;}
      .i{background-color: rgba(0,0,0,.6); position: absolute; left: 0; right: 0; bottom: 0; color: #fff; height: 20px; line-height: 20px;}
      .table-striped>tbody>tr:nth-child(odd)>td, .table-striped>tbody>tr:nth-child(odd)>th, .table-striped>thead>th {
            word-wrap: break-word;
            white-space: normal;
            word-break: break-all;
      }
      #detail_card #contents {
        word-wrap: break-word;
        white-space: normal;
        word-break: break-all;
      }
   </style>
</head>
<body>


<div class="page-main" style="top:0px;margin:15px">

<div class="page-tabs" style="top:0px;">

    <form method="post" action="" id="formsubmit">
        <div class="tool-bar ng-scope" style="background:#fff;border-bottom:1px solid #dfe0e0">
            <div class="bar-full">
                <a class="btn btn-addon btn-info ng-scope" href=""><i class="glyphicon glyphicon-plus"></i>刷新</a>
            </div>
            <div>
                <select class="form-control" name="shop_id">
                    <option value="">全部门店</option>
                    <?php foreach ($sshops as $sshop) { ?>
                    <option value="{$sshop.id}" <?php if (!empty($_POST['shop_id']) &&  $sshop['id'] == $_POST['shop_id']) { echo 'selected';}?>>{$sshop.username}</option>
                    <?php } ?>
                </select>
            </div>
            <div class="search-bool">
                <div class="input-group ng-pristine ng-valid">
                    <input type="text" class="form-control ng-pristine ng-untouched ng-valid" placeholder="商品名称" name="keyword" value="<?=$_POST['keyword']??''; ?>">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default" id="search_btn">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </form>

    <ul class="page-tabs-container">
        <li class="fadeIn animated active">
            <div class="table-container" style="top:10px;background:#fff">
                <table class="table table-striped table-hover">
                    <tbody>
                        <tr>
                            <td></td>
                            <?php foreach ($shops as $shop) { ?>
                                <td>{$shop['username']}</td>
                            <?php } ?>
                        </tr>
                        <?php foreach ($goods as $good) { ?>
                        <tr>
                            <td>{$good['store_name']}</td>
                            <?php 
                                foreach ($shops as $shop) {
                                    if (in_array($good['id'], $shop['hideProduct'])) {
                                        $check = '';
                                    } else {
                                        $check = 'checked';
                                    }
                            ?>
                            <td><input type='checkbox' {$check} name="hide" data-shop-id="{$shop.id}" data-admin-id="{$shop.admin_id}" data-product-id="{$good.id}" /></td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </li>
    </ul>
</div>
</div>

   

<div class="loading ng-hide" style="display: none;"> <div class="windows8"> <div class="wBall" id="wBall_1"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_2"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_3"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_4"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_5"> <div class="wInnerBall"></div> </div> </div> </div>
 
<form enctype="multipart/form-data" class="fileform2" style="display: none">
    <input type="file" name="img">
</form>

</body>
</html>
<script type="text/javascript">
    $(function(){
        $("[name='hide']").change(function(){
            var data = {
                'shop_id': $(this).data('shop-id'),
                'admin_id': $(this).data('admin-id'),
                'product_id': $(this).data('product-id'),
            };
            $.ajax({
                url: "/admin/shop.shopProduct/save.html",
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.code == 0) {

                    }
                    $(".loading").hide();
                }
            })
        });
   });
</script>
