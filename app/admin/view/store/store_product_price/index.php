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
                <select class="form-control" name="admin_id">
                    <option value="">全部品牌</option>
                    <?php foreach ($admins as $admin) { ?>
                    <option value="{$admin.id}" <?php if (!empty($_POST['admin_id']) && $admin['id'] == $_POST['admin_id']) { echo 'selected';}?>>{$admin.real_name}</option>
                    <?php } ?>
                </select>
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
                            <td>{$shop['admin']['real_name']}-{$shop['username']}</td>
                            <?php } ?>
                        </tr>
                        <?php foreach ($suks as $suk) { ?>
                        <tr>
                            <td>{$suk['product']['store_name']}-{$suk['suk']}</td>
                            <?php 
                                foreach ($shops as $shop) {
                                    $price = empty($shop['products'][$suk['product_id']][$suk['suk']]) ? $suk['price'] : $shop['products'][$suk['product_id']][$suk['suk']];
                            ?>
                            <td>
                                <?php if (isset($shop['products'][$suk['product_id']])) { ?>
                                <input type="text" size="6" value="{$price}" name="price" data-suk="{$suk['suk']}" data-product-id="{$suk['product_id']}" data-shop-id="{$shop['id']}" data-admin-id="{$shop['admin_id']}" />
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <?php
                                }
                            ?>
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
        $("[name='price']").change(function(){
            var data = {
                'price': $(this).val(),
                'shop_id': $(this).data('shop-id'),
                'admin_id': $(this).data('admin-id'),
                'product_id': $(this).data('product-id'),
                'suk': $(this).data('suk'),
            };
            $.ajax({
                url: "/admin/store.store_product_price/save.html",
                type: 'POST',
                data: data,
                success: function (data) {
                    if (data.code == 0) {

                    }
                    $(".loading").hide();
                }
            })
        });

    //      $(".edit").click(function () {
    //             var id = $(this).attr('data-id');
    //             $(".loading").show();
    //             $.ajax({
    //                 url: "/admin/shop.shopList/find.html",
    //                 type: 'POST',
    //                 data: { id: id },
    //                 success: function (data) {
    //                     if (data.code == 0) {
    //                         $("#edit_card [name='id']").val(data.data.id);
    //                         $("#edit_card [name='username']").val(data.data.username);
    //                         $("#edit_card [name='address']").val(data.data.username);
    //                         $("#edit_card [name='phone']").val(data.data.phone);
    //                         $("#edit_card").addClass('show');
    //                     } else {
    //                         swal("提示", data.msg, "warning");
    //                     }
    //                     $(".loading").hide();
    //                 }
    //             })
    //         });

    //     $(".delete").click(function () {
    //             var id = $(this).attr('data-id');
    //             swal({
    //                 title: "提示",
    //                 text: "确定要删除这条记录吗？",
    //                 type: "warning",
    //                 showCancelButton: true,
    //                 closeOnConfirm: false,
    //                 cancelButtonText: '取消',
    //                 confirmButtonText: '确定'
    //             }, function () {
    //                 $(".loading").show();
    //                 $.ajax({
    //                     url: "/admin/shop.shopList/del.html",
    //                     type: 'POST',
    //                     data: { id: id },
    //                     success: function (data) {
    //                         if (data.code == 0) {
    //                             swal({ title: "提示", text: "删除成功", type: "success", timer: 1500, }, function () {
    //                                 window.location.reload();
    //                             });
    //                         } else {
    //                             swal("提示", data.msg, "warning");
    //                         }
    //                         $(".loading").hide();
    //                     }
    //                 })
    //             });
    //         });

    //     //点击确定
    //     $("#add_action").click(function () {
    //             var data = {
    //                 username: $("#add_card [name='username']").val(),
    //                 address: $("#add_card [name='address']").val(),
    //                 phone: $("#add_card [name='phone']").val(),
    //             };

    //             if (data.username == '') {
    //                 swal({ title: "提示", text: "门店名称不能为空", type: "warning" }, function () {
    //                     $("#add_card [name='username']").focus();
    //                 });
    //                 return false;
    //             }
    //             if (data.address == '') {
    //                 swal({ title: "提示", text: "门店地址不能为空", type: "warning" }, function () {
    //                     $("#add_card [name='address']").focus();
    //                 });
    //                 return false;
    //             }
            
    //             if (data.phone == '') {
    //                 swal({ title: "提示", text: "联系方式不能为空", type: "warning" }, function () {
    //                     $("#add_card [name='phone']").focus();
    //                 });
    //                 return false;
    //             }
    //             $(".loading").show();
    //             $.ajax({
    //                 type: 'POST',
    //                 url: "/admin/shop.shopList/add.html",
    //                 data: data,
    //                 success: function (data) {
    //                     if (data.code == 0) {
    //                         swal({ title: "提示", text: "新增成功", type: "success", timer: 1500, }, function () {
    //                             window.location.reload();
    //                         });
    //                     } else {
    //                         swal("提示", data.msg, "warning");
    //                     }
    //                     $(".loading").hide();
    //                 }
    //             });
    //         });

    //         $("#edit_action").click(function () {
    //             var data = {
    //                 id: $("#edit_card [name='id']").val(),
    //                 password: $("#edit_card [name='password']").val(),
    //                 username: $("#edit_card [name='username']").val(),
    //                 address: $("#edit_card [name='address']").val(),
    //                 phone: $("#edit_card [name='phone']").val(),
    //             };

    //             if (data.username == '') {
    //                 swal({ title: "提示", text: "门店名称不能为空", type: "warning" }, function () {
    //                     $("#edit_card [name='username']").focus();
    //                 });
    //                 return false;
    //             }
    //             if (data.address == '') {
    //                 swal({ title: "提示", text: "门店地址不能为空", type: "warning" }, function () {
    //                     $("#edit_card [name='address']").focus();
    //                 });
    //                 return false;
    //             }
            
    //             if (data.phone == '') {
    //                 swal({ title: "提示", text: "联系方式不能为空", type: "warning" }, function () {
    //                     $("#edit_card [name='phone']").focus();
    //                 });
    //                 return false;
    //             }
    //             $(".loading").show();
    //             $.ajax({
    //                 type: 'POST',
    //                 url: "/admin/shop.shopList/save.html",
    //                 data: data,
    //                 success: function (data) {
    //                     if (data.code == 0) {
    //                         swal({ title: "提示", text: "保存成功", type: "success", timer: 1500, }, function () {
    //                             window.location.reload();
    //                         });
    //                     } else {
    //                         swal("提示", data.msg, "warning");
    //                     }
    //                     $(".loading").hide();
    //                 }
    //             });
    //         });

    //     $("#add").click(function(){
    //         $("#add_card").addClass('show');
    //     });

    //    $('.selectpicker').selectpicker({
    //        selectAllText:'全选',
    //        deselectAllText:'全不选',
    //        noneSelectedText:'请选择',
    //        size:5
    //    });

    //    $('.colorpicker').colorpicker();
    //   //关闭弹窗
    //   $('.info-card-close').click(function(event){
    //      var a = event.target;
    //      $(a).closest('.info-card').removeClass("show");
    //   });
    //   $(".user-nav").click(function(){
    //      $(this).addClass('active');
    //      $("body").one("click",function(){
    //          $('.user-nav').removeClass('active');
    //      });
    //      return false;
    //   });

    //   //显示删除按钮
    //    $(document).on('mouseenter', '.album-plus', function () {
    //        if ($(this).find('img').attr('src') != '') {
    //            $(this).find('i').removeClass('hide');
    //        }
    //    });

    //    $(document).on('mouseleave', '.album-plus', function(){
    //        $(this).find('i').addClass('hide');
    //    });
       
    //    $(document).on('click', '.icon-trash', function () {
    //        $(this).parent("li").find('span').removeClass('hide');
    //        $(this).parent("li").find('img').attr('src', '');
    //        return false;
    //    });

    //    var index;
    //    $(document).on('click', '.album-plus', function () {
    //        index = $('.album-plus').index(this);
    //        $(".fileform2 input[name='img']").trigger('click');
    //    });

    //    $(".fileform2 input[name='img']").change(function () {
    //        if ($(this).val() == '') return false;
    //        if ($(this)[0].files[0].size > 1024 * 1024 * 10) {
    //            $(".fileform2 input[name='img']").val('');
    //            swal("提示", '图片大小不能超过10M', "warning");
    //            return false;
    //        }
    //        $(".loading").show();
    //        $.ajax({
    //            url: "",
    //            type: 'POST',
    //            cache: false,
    //            data: new FormData($('.fileform2')[0]),
    //            processData: false,
    //            contentType: false
    //        }).done(function (res) {
    //            $(".fileform2 input[name='img']").val('');
    //            if (res.code == 0) {
    //                $(".album-plus:eq(" + index + ") span").addClass('hide');
    //                $(".album-plus:eq(" + index + ") img").attr('src', res.data.imgurl);
    //                $(".album-plus:eq(" + index + ") img").attr('data-name', res.data.name);
    //            } else {
    //                swal("提示", res.msg, "warning");
    //            }
    //            $(".loading").hide();
    //        });
    //    });


    //   $("#editPassword").click(function(){
    //      $("#password_info").addClass('show');
    //   });

    //   $("#loginOut").click(function(){
    //      swal({
    //             title: "提示",  
    //             text: "确定要退出吗？",  
    //             type: "warning", 
    //             showCancelButton: true, 
    //             closeOnConfirm: false, 
    //             cancelButtonText: '取消', 
    //             confirmButtonText: '确定'
    //      }, function(){
    //            location.href='';
    //      });
    //   });

    //   //保存修改密码
    //   $("#save_password").click(function(){
    //      var password    = $("input[name='password']").val();
    //      var newpassword = $("input[name='newpassword']").val();
    //      var repassword  = $("input[name='repassword']").val();
    //      if(!password){
    //          swal({title: "提示", text: "原密码不能为空", type: "warning"}, function(){
    //              $("input[name='password']").focus();
    //          });
    //          return false;
    //      } else {
    //         if (password.length < 6) {
    //             swal({ title: "提示", text: "密码必须大于等于6位", type: "warning" }, function () {
    //                 $("input[name='password']").focus();
    //             });
    //             return false;
    //         }
    //      }
    //      if(!newpassword){
    //          swal({title: "提示", text: "新密码不能为空", type: "warning"}, function(){
    //              $("input[name='newpassword']").focus();
    //          });
    //          return false;
    //      } else {
    //         if (newpassword.length < 6) {
    //              swal({ title: "提示", text: "密码必须大于等于6位", type: "warning" }, function () {
    //                  $("input[name='newpassword']").focus();
    //              });
    //              return false;
    //          }
    //      }
    //      if(!repassword){
    //          swal({title: "提示", text: "重复密码不能为空", type: "warning"}, function(){
    //              $("input[name='repassword']").focus();
    //          });
    //          return false;
    //      }
    //      if(newpassword != repassword){
    //          swal({title: "提示", text: "新密码和重复密码不一致", type: "warning"});
    //          return false;
    //      }
    //      $.ajax({
    //          type:'post',
    //          url:'',
    //          data:{password:password, newpassword:newpassword, repassword:repassword},
    //          success:function(data){
    //              if(data.code == 0){
    //                  swal({title: "提示", text: "修改成功", type: "success"});
    //                  $("#password_info").removeClass('show');
    //              }else{
    //                  swal({title: "提示", text: data.msg, type: "warning"});
    //              }
                 
    //          }
    //      });
    //   });

   });
</script>
