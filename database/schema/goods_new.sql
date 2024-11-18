-- 货物品牌
DROP TABLE IF EXISTS `sys_goods_brand`;
CREATE TABLE `sys_goods_brand` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌名称',
    `letter` char(1) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌名称首字母(大写)',
    `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌图片地址',
    `description` varchar(255) COLLATE utf8mb4_unicode_ci NULL COMMENT '品牌描述',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 325403 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '货物品牌';


-- 货物分类(无限级)
DROP TABLE IF EXISTS `sys_goods_category`;
CREATE TABLE `sys_goods_category` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
    `parent_id` bigint(20) UNSIGNED NOT NULL COMMENT '父类目id, 顶级类目填0',
    `is_parent` tinyint(1) UNSIGNED NOT NULL COMMENT '是否为父节点: 0 - 否, 1 - 是',
    `sort` tinyint(4) UNSIGNED NOT NULL COMMENT '排序指数, 越小越靠前',
    `description` varchar(255) COLLATE utf8mb4_unicode_ci NULL COMMENT '分类描述',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 1424 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '货物分类(无限级)';


-- 货物品牌与分类的关联表
DROP TABLE IF EXISTS `sys_slot_goods_category_brand`;
DROP TABLE IF EXISTS `sys_pivot_goods_category_brand`;
CREATE TABLE `sys_pivot_goods_category_brand` (
    `category_id` bigint(20) UNSIGNED NOT NULL COMMENT '分类id(外键-关联到sys_goods_category表id)',
    `brand_id` bigint(20) UNSIGNED NOT NULL COMMENT '品牌id(外键-关联到sys_goods_brand表id)',
    PRIMARY KEY (`category_id`, `brand_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '【中间关联表】货物品牌与分类的关联表';


-- 货物参数组
DROP TABLE IF EXISTS `sys_goods_spec_group`;
CREATE TABLE `sys_goods_spec_group` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_id` bigint(20) UNSIGNED NOT NULL COMMENT '分类id(外键-关联到sys_goods_category表id)',
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '参数组名称',
    `parent_id` bigint(20) UNSIGNED NOT NULL COMMENT '父类目id, 顶级类目填0',
    `is_parent` tinyint(1) UNSIGNED NOT NULL COMMENT '是否为父节点: 0 - 否, 1 - 是',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 15 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '货物参数组';


-- 货物SPU
DROP TABLE IF EXISTS `sys_goods_spu`;
CREATE TABLE `sys_goods_spu` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SPU名称',
    `sub_title` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT 'SPU副标题名称',
    `category_id` bigint(20) UNSIGNED NOT NULL COMMENT '分类id(外键-关联到sys_goods_category表id)',
    `brand_id` bigint(20) UNSIGNED NOT NULL COMMENT '品牌id(外键-关联到sys_goods_brand表id)',
    `saleable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否上架: 0 - 否, 1 - 是',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 187 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '货物SPU';


-- 货物SPU详情
DROP TABLE IF EXISTS `sys_goods_spu_detail`;
CREATE TABLE `sys_goods_spu_detail` (
    `spu_id` bigint(20) UNSIGNED NOT NULL COMMENT 'spu id(外键-关联到sys_goods_spu表id)',
    `description` longtext COLLATE utf8mb4_unicode_ci NULL COMMENT 'SPU详情描述信息(富文本)',
    `images` longtext COLLATE utf8mb4_unicode_ci NULL COMMENT 'SPU图片组, json数组格式',
    `packing_list` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '包装清单',
    `after_service` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '售后服务',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`spu_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '货物SPU详情';


-- 货物参数组与货物SPU的关联表
DROP TABLE IF EXISTS `sys_slot_goods_spec_group_spu`;
CREATE TABLE `sys_slot_goods_spec_group_spu` (
    `spec_group_id` bigint(20) UNSIGNED NOT NULL COMMENT '货物参数组id(外键-关联到sys_goods_spec_group表id)',
    `spu_id` bigint(20) UNSIGNED NOT NULL COMMENT 'spu id(外键-关联到sys_goods_spu表id)',
    PRIMARY KEY (`spec_group_id`, `spu_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '【中间关联表】货物参数组与货物SPU的关联表';


-- 服务时间表
DROP TABLE IF EXISTS `sys_goods_service_time`;
CREATE TABLE `sys_goods_service_time`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL COMMENT '服务日期',
    `start_time` TIME NOT NULL COMMENT '时间段的开始时间',
    `end_time` TIME NOT NULL COMMENT '时间段的结束时间',
    `enable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否有效: 0 - 否, 1 - 是',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 312 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品服务时间区间表';


-- 服务时间表与货物SPU的关联表
DROP TABLE IF EXISTS `sys_slot_goods_service_time_spu`;
CREATE TABLE `sys_slot_goods_service_time_spu` (
    `service_time_id` bigint(20) UNSIGNED NOT NULL COMMENT '服务时间id(外键-关联到sys_goods_service_time表id)',
    `spu_id` bigint(20) UNSIGNED NOT NULL COMMENT 'spu id(外键-关联到sys_goods_spu表id)',
    `stock` int(8) UNSIGNED NOT NULL COMMENT '库存',
    PRIMARY KEY (`service_time_id`, `spu_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '【中间关联表】服务时间表与货物SPU的关联表';


-- 宠物品种表
DROP TABLE IF EXISTS `sys_pet_breed`;
CREATE TABLE `sys_pet_breed`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '宠物品种类型: 1 - 猫, 2 - 狗',
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '宠物品种名称',
    `letter` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '宠物品种名称首字母(大写)',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 618 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '宠物品种表';


-- 宠物品种体重表
DROP TABLE IF EXISTS `sys_pet_breed_weight`;
CREATE TABLE `sys_pet_breed_weight`  (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `breed_id` bigint(20) UNSIGNED NOT NULL COMMENT '宠物品种id(外键-关联到sys_pet_breed表id)',
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '体重名称',
    `min` int(10) UNSIGNED NOT NULL COMMENT '体重最小值，单位为公斤',
    `max` int(10) UNSIGNED NOT NULL COMMENT '体重最大值，单位为公斤',
    `weight_type` tinyint(1) UNSIGNED NULL COMMENT '体重规格: 0 - 全体型, 1 - 小, 2 - 中, 3 - 大',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 1111 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '宠物品种体重表';


-- 货物SKU
DROP TABLE IF EXISTS `sys_goods_sku`;
CREATE TABLE `sys_goods_sku` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `spu_id` bigint(20) UNSIGNED NOT NULL COMMENT 'spu id(外键-关联到sys_goods_spu表id)',
    `images` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '商品的图片, 多个图片以\',\'分隔',
    `enable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否有效: 0 - 否, 1 - 是',
    `morph` tinyint(1) UNSIGNED NOT NULL DEFAULT false COMMENT '是否多态关联: 0 - 否, 1 - 是(当为多态关联时，显示多态所关联模型的title和价格，否则，显示当前的title和价格，保存时亦然)',
    `title` varchar(64) COLLATE utf8mb4_unicode_ci NULL COMMENT 'SPU名称',
    `stock` int(8) UNSIGNED NULL COMMENT '库存',
    `price` bigint(16) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售价格, 单位为分',
    `created_at` timestamp NOT NULL,
    `updated_at` timestamp NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = INNODB AUTO_INCREMENT = 27359021564 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '货物SKU';


-- 货物参数组与货物SKU的关联表
DROP TABLE IF EXISTS `sys_slot_goods_sku_spec_group`;
CREATE TABLE `sys_slot_goods_sku_spec_group` (
    `spu_id` bigint(20) UNSIGNED NOT NULL COMMENT 'spu id(外键-关联到sys_goods_spu表id)',
    `sku_id` bigint(20) UNSIGNED NOT NULL COMMENT 'sku id(外键-关联到sys_goods_sku表id)',
    `spec_group_id` bigint(20) UNSIGNED NOT NULL COMMENT '规格组id(外键-关联到sys_goods_spec_group表id)',
    `spec_value_id` bigint(20) UNSIGNED NOT NULL COMMENT '规格值id（可以是sys_pet_breed或sys_pet_breed_weight表的id）',
    `taggable_type` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '与之产生关系的类名',
    PRIMARY KEY (`sku_id`, `spec_group_id`, `spec_value_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '【中间关联表】货物参数组与货物SKU的关联表';

-- 查询示例
-- SELECT s.price FROM sys_goods_sku s JOIN sys_slot_goods_sku_spec_group ssg ON s.id = ssg.sku_id
-- WHERE ssp.spec_group_id IN (SELECT id FROM sys_goods_spec_group WHERE title IN ('宠物品种', '宠物体重'))
-- AND ssg.spec_value_id IN (SELECT id FROM sys_pet_breed WHERE title = '泰迪')
-- AND ssg.spec_value_id IN (SELECT id FROM sys_pet_breed_weight WHERE min <= 5 AND max >= 5)