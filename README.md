v1.0.4数据库变动

eb_store_combination 增加字段 status 状态 
eb_store_combination 增加字段 storage_price 仓储费
eb_store_order 增加字段 storage_price 仓储费
eb_store_order 增加字段 user_store_id 用户库存id
增加表 eb_user_stock
eb_store_cart 增加 user_stock_id 用户库存id

eb_store_cart 增加 takeout_id 自提-用户库存id
eb_store_order 增加 takeout_id 自提-用户库存id
eb_store_product 增加 delivery_fee 配送费

eb_store_combination one_commission 一级佣金
eb_store_combination two_commission 二级佣金


供应链小程序
eb_user 增加字段 is_supply_chain shop_id

eb_shop门店表

eb_store_product 增加admin_id

删除门店时删除user表的中shop_id