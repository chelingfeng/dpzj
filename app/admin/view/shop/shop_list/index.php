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
                <a class="btn btn-addon btn-info ng-scope" id="add"><i
                        class="glyphicon glyphicon-plus"></i>新增门店</a>
            </div>

            <div class="search-bool">


            </div>
        </div>
    </form>

    <ul class="page-tabs-container">
        <li class="fadeIn animated active">
            <div class="table-container" style="top:10px;background:#fff">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>门店名称</th>
                            <th>品牌方</th>
                            <th>门店地址</th>
                            <th>联系方式</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($list as $v) { ?>
                       <tr>
                           <td>{$v.username}</td>
                           <td>
                            {$v['admin']['real_name']}
                           </td>
                           <td>{$v.address}</td>
                           <td>{$v.phone}</td>
                           <td>
                              <a class="btn btn-info btn-xs edit" data-id="{$v.id}">编辑</a>
                              <a class="btn btn-danger btn-xs delete" data-id="{$v.id}">删除</a>
                           </td>
                       </tr>
                       <?php } ?>
                        
                    <?php if (empty($list)) { ?>
                    <tfoot>
                        <tr>
                            <td colspan="20" class="empty">没有检索到相关数据</td>
                        </tr>
                    </tfoot>
                    <?php } ?>
                   
                    </tbody>
                </table>
            </div>

        </li>
    </ul>
</div>
</div>

<div class="info-card fadeIn animated ng-scope" id="add_card">
        <div class="info-card-wrapper">
            <div class="info-card-dialog">
                <div class="info-card-content">
                    <div class="panel panel-info">
                        <div class="panel-heading"> 新增门店
                            <a class="info-card-close"><i class="ALiconfont icon-close"></i></a>
                        </div>
                        <div class="panel-body">
                            <div class="form-tabs">
                                <ul class="form-tabs-container">
                                    <li class="fadeIn animated active">
                                        <table class="table-bordered bg-white table-form">
                                            <tbody>
                                                <tr>
                                                    <td class="require">门店名称</td>
                                                    <td>
                                                        <input type="text" name="username">
                                                    </td>
                                                </tr>
                                                <tr class="">
                                                    <td class="require">门店地址</td>
                                                    <td>
                                                        <input type="text" name="address">
                                                    </td>
                                                </tr>
                                                <tr class="">
                                                    <td class="require">联系方式</td>
                                                    <td>
                                                        <input type="text" name="phone">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="td-btn-box" colspan="2">
                                                        <a class="btn btn-info" id="add_action">确定</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="info-card fadeIn animated ng-scope" id="edit_card">
        <div class="info-card-wrapper">
            <div class="info-card-dialog">
                <div class="info-card-content">
                    <div class="panel panel-info">
                        <div class="panel-heading"> 编辑门店
                            <a class="info-card-close"><i class="ALiconfont icon-close"></i></a>
                        </div>
                        <div class="panel-body">
                            <div class="form-tabs">
                                <ul class="form-tabs-container">
                                    <li class="fadeIn animated active">
                                        <table class="table-bordered bg-white table-form">
                                            <tbody>
                                                <tr>
                                                    <td class="require">门店名称</td>
                                                    <td>
                                                        <input type="text" name="username" disabled>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="">密码</td>
                                                    <td>
                                                        <input type="password" name="password">
                                                    </td>
                                                </tr>
                                                <tr class="">
                                                    <td class="require">门店地址</td>
                                                    <td>
                                                        <input type="text" name="address">
                                                    </td>
                                                </tr>
                                                <tr class="">
                                                    <td class="require">联系方式</td>
                                                    <td>
                                                        <input type="text" name="phone">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="td-btn-box" colspan="2">
                                                        <input type="hidden" value="" name="id" />
                                                        <a class="btn btn-info" id="edit_action">确定</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="loading ng-hide" style="display: none;"> <div class="windows8"> <div class="wBall" id="wBall_1"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_2"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_3"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_4"> <div class="wInnerBall"></div> </div> <div class="wBall" id="wBall_5"> <div class="wInnerBall"></div> </div> </div> </div>
 
<form enctype="multipart/form-data" class="fileform2" style="display: none">
    <input type="file" name="img">
</form>

</body>
</html>
<script type="text/javascript">
    function isURL(str) {
        var RegUrl = new RegExp();
        RegUrl.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");//jihua.cnblogs.com 
        if (!RegUrl.test(str)) {
            return false;
        }
        return true;
    } 
   $(function(){


         $(".edit").click(function () {
                var id = $(this).attr('data-id');
                $(".loading").show();
                $.ajax({
                    url: "/admin/shop.shopList/find.html",
                    type: 'POST',
                    data: { id: id },
                    success: function (data) {
                        if (data.code == 0) {
                            $("#edit_card [name='id']").val(data.data.id);
                            $("#edit_card [name='username']").val(data.data.username);
                            $("#edit_card [name='address']").val(data.data.username);
                            $("#edit_card [name='phone']").val(data.data.phone);
                            $("#edit_card").addClass('show');
                        } else {
                            swal("提示", data.msg, "warning");
                        }
                        $(".loading").hide();
                    }
                })
            });

        $(".delete").click(function () {
                var id = $(this).attr('data-id');
                swal({
                    title: "提示",
                    text: "确定要删除这条记录吗？",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    cancelButtonText: '取消',
                    confirmButtonText: '确定'
                }, function () {
                    $(".loading").show();
                    $.ajax({
                        url: "/admin/shop.shopList/del.html",
                        type: 'POST',
                        data: { id: id },
                        success: function (data) {
                            if (data.code == 0) {
                                swal({ title: "提示", text: "删除成功", type: "success", timer: 1500, }, function () {
                                    window.location.reload();
                                });
                            } else {
                                swal("提示", data.msg, "warning");
                            }
                            $(".loading").hide();
                        }
                    })
                });
            });

        //点击确定
        $("#add_action").click(function () {
                var data = {
                    username: $("#add_card [name='username']").val(),
                    address: $("#add_card [name='address']").val(),
                    phone: $("#add_card [name='phone']").val(),
                };

                if (data.username == '') {
                    swal({ title: "提示", text: "门店名称不能为空", type: "warning" }, function () {
                        $("#add_card [name='username']").focus();
                    });
                    return false;
                }
                if (data.address == '') {
                    swal({ title: "提示", text: "门店地址不能为空", type: "warning" }, function () {
                        $("#add_card [name='address']").focus();
                    });
                    return false;
                }
            
                if (data.phone == '') {
                    swal({ title: "提示", text: "联系方式不能为空", type: "warning" }, function () {
                        $("#add_card [name='phone']").focus();
                    });
                    return false;
                }
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: "/admin/shop.shopList/add.html",
                    data: data,
                    success: function (data) {
                        if (data.code == 0) {
                            swal({ title: "提示", text: "新增成功", type: "success", timer: 1500, }, function () {
                                window.location.reload();
                            });
                        } else {
                            swal("提示", data.msg, "warning");
                        }
                        $(".loading").hide();
                    }
                });
            });

            $("#edit_action").click(function () {
                var data = {
                    id: $("#edit_card [name='id']").val(),
                    password: $("#edit_card [name='password']").val(),
                    username: $("#edit_card [name='username']").val(),
                    address: $("#edit_card [name='address']").val(),
                    phone: $("#edit_card [name='phone']").val(),
                };

                if (data.username == '') {
                    swal({ title: "提示", text: "门店名称不能为空", type: "warning" }, function () {
                        $("#edit_card [name='username']").focus();
                    });
                    return false;
                }
                if (data.address == '') {
                    swal({ title: "提示", text: "门店地址不能为空", type: "warning" }, function () {
                        $("#edit_card [name='address']").focus();
                    });
                    return false;
                }
            
                if (data.phone == '') {
                    swal({ title: "提示", text: "联系方式不能为空", type: "warning" }, function () {
                        $("#edit_card [name='phone']").focus();
                    });
                    return false;
                }
                $(".loading").show();
                $.ajax({
                    type: 'POST',
                    url: "/admin/shop.shopList/save.html",
                    data: data,
                    success: function (data) {
                        if (data.code == 0) {
                            swal({ title: "提示", text: "保存成功", type: "success", timer: 1500, }, function () {
                                window.location.reload();
                            });
                        } else {
                            swal("提示", data.msg, "warning");
                        }
                        $(".loading").hide();
                    }
                });
            });

        $("#add").click(function(){
            $("#add_card").addClass('show');
        });

       $('.selectpicker').selectpicker({
           selectAllText:'全选',
           deselectAllText:'全不选',
           noneSelectedText:'请选择',
           size:5
       });

       $('.colorpicker').colorpicker();
      //关闭弹窗
      $('.info-card-close').click(function(event){
         var a = event.target;
         $(a).closest('.info-card').removeClass("show");
      });
      $(".user-nav").click(function(){
         $(this).addClass('active');
         $("body").one("click",function(){
             $('.user-nav').removeClass('active');
         });
         return false;
      });

      //显示删除按钮
       $(document).on('mouseenter', '.album-plus', function () {
           if ($(this).find('img').attr('src') != '') {
               $(this).find('i').removeClass('hide');
           }
       });

       $(document).on('mouseleave', '.album-plus', function(){
           $(this).find('i').addClass('hide');
       });
       
       $(document).on('click', '.icon-trash', function () {
           $(this).parent("li").find('span').removeClass('hide');
           $(this).parent("li").find('img').attr('src', '');
           return false;
       });

       var index;
       $(document).on('click', '.album-plus', function () {
           index = $('.album-plus').index(this);
           $(".fileform2 input[name='img']").trigger('click');
       });

       $(".fileform2 input[name='img']").change(function () {
           if ($(this).val() == '') return false;
           if ($(this)[0].files[0].size > 1024 * 1024 * 10) {
               $(".fileform2 input[name='img']").val('');
               swal("提示", '图片大小不能超过10M', "warning");
               return false;
           }
           $(".loading").show();
           $.ajax({
               url: "",
               type: 'POST',
               cache: false,
               data: new FormData($('.fileform2')[0]),
               processData: false,
               contentType: false
           }).done(function (res) {
               $(".fileform2 input[name='img']").val('');
               if (res.code == 0) {
                   $(".album-plus:eq(" + index + ") span").addClass('hide');
                   $(".album-plus:eq(" + index + ") img").attr('src', res.data.imgurl);
                   $(".album-plus:eq(" + index + ") img").attr('data-name', res.data.name);
               } else {
                   swal("提示", res.msg, "warning");
               }
               $(".loading").hide();
           });
       });


      $("#editPassword").click(function(){
         $("#password_info").addClass('show');
      });

      $("#loginOut").click(function(){
         swal({
                title: "提示",  
                text: "确定要退出吗？",  
                type: "warning", 
                showCancelButton: true, 
                closeOnConfirm: false, 
                cancelButtonText: '取消', 
                confirmButtonText: '确定'
         }, function(){
               location.href='';
         });
      });

      //保存修改密码
      $("#save_password").click(function(){
         var password    = $("input[name='password']").val();
         var newpassword = $("input[name='newpassword']").val();
         var repassword  = $("input[name='repassword']").val();
         if(!password){
             swal({title: "提示", text: "原密码不能为空", type: "warning"}, function(){
                 $("input[name='password']").focus();
             });
             return false;
         } else {
            if (password.length < 6) {
                swal({ title: "提示", text: "密码必须大于等于6位", type: "warning" }, function () {
                    $("input[name='password']").focus();
                });
                return false;
            }
         }
         if(!newpassword){
             swal({title: "提示", text: "新密码不能为空", type: "warning"}, function(){
                 $("input[name='newpassword']").focus();
             });
             return false;
         } else {
            if (newpassword.length < 6) {
                 swal({ title: "提示", text: "密码必须大于等于6位", type: "warning" }, function () {
                     $("input[name='newpassword']").focus();
                 });
                 return false;
             }
         }
         if(!repassword){
             swal({title: "提示", text: "重复密码不能为空", type: "warning"}, function(){
                 $("input[name='repassword']").focus();
             });
             return false;
         }
         if(newpassword != repassword){
             swal({title: "提示", text: "新密码和重复密码不一致", type: "warning"});
             return false;
         }
         $.ajax({
             type:'post',
             url:'',
             data:{password:password, newpassword:newpassword, repassword:repassword},
             success:function(data){
                 if(data.code == 0){
                     swal({title: "提示", text: "修改成功", type: "success"});
                     $("#password_info").removeClass('show');
                 }else{
                     swal({title: "提示", text: data.msg, type: "warning"});
                 }
                 
             }
         });
      });

   });
</script>
