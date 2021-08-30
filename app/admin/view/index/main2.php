{extend name="public/container"}
{block name="head_top"}
<!-- 全局js -->
<script src="{__PLUG_PATH}echarts/echarts.common.min.js"></script>
<script src="{__PLUG_PATH}echarts/theme/macarons.js"></script>
<script src="{__PLUG_PATH}echarts/theme/westeros.js"></script>
{/block}
{block name="content"}
    <div class="row">
        <div class="col-sm-3 ui-sortable">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-danger pull-right">总</span>
                    <h5>订单</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{$orderNum}</h1>
                    <small><a href="javascript:;" data-name="订单管理">总支付订单数</a> </small>
                </div>
            </div>
        </div>
        <div class="col-sm-3 ui-sortable">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">今</span>
                    <h5>订单</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{$todayOrderNum}</h1>
                    <small><a href="javascript:;" data-name="订单管理">今日支付订单数</a></small>
                </div>
            </div>
        </div>
        <div class="col-sm-3 ui-sortable">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-danger pull-right">总</span>
                    <h5>交易额</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{$allPayPrice}</h1>
                    <small><a href="javascript:;" class="opFrames" data-name="订单管理">总交易额</a></small>
                </div>
            </div>
        </div>
        <div class="col-sm-3 ui-sortable">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">今</span>
                    <h5>交易额</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins">{$todayPayPrice}</h1>
                    <small><a href="javascript:;" class="opFrames" data-name="订单管理">今日交易额</a></small>
                </div>
            </div>
        </div>
    </div>
<div id="app">
    <div class="row" >
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>商品销售统计</h5>
                </div>
                <div class="ibox-content">
                    <p>
                        <form method="post" action="">
                            <select name="shop_id">
                                <option value="">全部门店</option>
                                <?php foreach ($sshops as $shop) { ?>
                                <option value="{$shop.id}" <?php if (!empty($_POST['shop_id']) && $_POST['shop_id'] == $shop['id']) {echo 'selected';}?>>{$shop.username}</option>
                                <?php } ?>
                            </select>
                        </form>
                    </p>
                    <div class="row">
                        <div class="col-lg-12">
                            <table lay-skin="line" class="layui-table layuiadmin-page-table">
                                <thead>
                                    <tr>
                                        <th>商品名称</th>
                                        <th>销量</th>
                                        <th>销售额</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($goods as $g) { ?>
                                    <tr>
                                        <td><span class="first">{$g['store_name']}</span></td>
                                        <td><span>{$g['num']}</span></td>
                                        <td>{$g['price']}</td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<style scoped>
    .box{width:0px;}
</style>
<script>
    $(function(){
        $("[name='shop_id']").change(function(){
            $("form").submit();
        });
    })
</script>
{/block}
