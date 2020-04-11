{extend name="public/container"}
{block name="content"}
<div class="ibox-content order-info">

    <div class="row">
        <table class="" id="print" style="width:100%;color:#000;line-height:35px;text-align:center">
            <tbody>
                <tr>
                    <td colspan="3" style="font-size:18px;line-height:40px">{:sys_config('print_title')}</td>
                </tr>
                <tr>
                    <td style="text-align:left;line-height:35px">客户名称：<span>{$orderInfo.real_name}</span></td>
                    <td style="line-height:35px">订单创建时间：<span>{$orderInfo.add_time|date="Y-m-d H:i:s"}</span></td>
                    <td style="line-height:35px">单据编号：<span>{$orderInfo.order_id}</span></td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table cellspacing="0" cellpadding="5" border="1" style="text-align:center;min-width:100%;border-collapse:collapse;line-height:25px" bordercolor="#000">
                            <tbody>
                                <tr class="goods-th">
                                    <td>序号</td>
                                    <td>商品名称</td>
                                    <td>购买数量</td>
                                    <td>计量单位</td>
                                    <td>商品价格</td>
                                    <td>金额小计</td>
                                    <td>商品备注</td>
                                </tr>
                                {foreach $_info as $key => $info} 
                                <tr>
                                    <td>{$key+1}</td>
                                    <td>{$info.cart_info.productInfo.store_name}</td>
                                    <td>{$info.cart_info.cart_num}</td>
                                    <td>{$info.cart_info.productInfo.attrInfo.suk}</td>
                                    <td>{$info.cart_info.productInfo.price}</td>
                                    <td>{:sprintf("%.2f", $info['cart_info']['productInfo']['price'] * $info['cart_info']['cart_num'])}</td>
                                    <td></td>
                                </tr>
                                {/foreach}
                                <tr>
                                    <td>总计</td>
                                    <td></td>
                                    <td>{$orderInfo.total_num}</td>
                                    <td></td>
                                    <td></td>
                                    <td>{$orderInfo.total_price}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>审核人：<span></span></td>
                    <td>送货人：<span></span></td>
                    <td>收货人：<span></span></td>
                </tr>
                <tr>
                    <td colspan="2">银行：{:sys_config('print_bank_number')}（{:sys_config('print_bank_name')}）</td>
                    <td colspan="2">微信/支付宝：{:sys_config('print_wechat_alipay')}</td>
                </tr>
            </tbody>
        </table>

        <div style="text-align:center;margin-top:25px">
            <a class="btn btn-success" id="print_btn" style="width:100px;">打印</a>
        </div>
         
    </div>
</div>
<script src="{__FRAME_PATH}js/content.min.js?v=1.0.0"></script>
<script src="{__FRAME_PATH}js/jquery.jqprint-0.3.js"></script>
<script src="{__FRAME_PATH}js/jquery-migrate-1.2.1.min.js"></script>
{/block}
{block name="script"}
<script>
$(function(){
    $("#print_btn").click(function(){
        $("#print").jqprint();
    });
})
</script>
{/block}
