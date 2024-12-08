-- 宠物品种表
DROP TABLE IF EXISTS sys_pet_breed;
CREATE TABLE `sys_goods_service_time`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL COMMENT '服务日期',
  `start_time` TIME NOT NULL COMMENT '时间段的开始时间',
  `end_time` TIME NOT NULL COMMENT '时间段的结束时间',
  `enable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否有效: 0 - 否, 1 - 是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品服务时间区间表';

-- 分类品牌关联中间表
DROP TABLE IF EXISTS sys_goods_category_brand_slots;
CREATE TABLE `sys_goods_category_brand_slots`  (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `brand_slot_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`category_id`, `brand_slot_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '【中间关联表】分类和品牌';

-- 商品参数-规格参数表
DROP TABLE IF EXISTS sys_goods_spec_param;
CREATE TABLE `sys_goods_spec_param`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品分类id',
  `group_id` bigint(20) UNSIGNED NOT NULL COMMENT '规格组id',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '参数名',
  `numeric` tinyint(1) UNSIGNED NOT NULL COMMENT '是否是数字类型参数: 0 - 否, 1 - 是',
  `unit` varchar(128) COLLATE utf8mb4_unicode_ci NULL COMMENT '数字类型参数的单位, 非数字类型可以为空',
  `generic` tinyint(1) UNSIGNED NOT NULL COMMENT '是否是 sku 通用属性: 0 - 否, 1 - 是',
  `searching` tinyint(1) UNSIGNED NOT NULL COMMENT '是否用于搜索过滤: 0 - 否, 1 - 是',
  `segments` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '数值类型参数, 如果需要搜索, 则添加分段间隔值, 如: CPU频率间隔: 0.5-1.0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `key_group_id`(`group_id`) USING BTREE,
  INDEX `key_category_id`(`category_id`) USING BTREE
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '规格参数表';

-- 商品参数-参数组表
DROP TABLE IF EXISTS sys_goods_spec_group;
CREATE TABLE `sys_goods_spec_group`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品分类id, 一个分类下有多个规格组',
  `title` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规格组的名称',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `key_category_id`(`category_id`) USING BTREE
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '参数组表';

-- 商品spu详情表
DROP TABLE IF EXISTS sys_goods_spu_detail;
CREATE TABLE `sys_goods_spu_detail`  (
  `spu_id` bigint(20) UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NULL COMMENT '商品描述信息',
  `generic_spec` varchar(2048) COLLATE utf8mb4_unicode_ci NULL COMMENT '通用规格参数数据',
  `special_spec` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '特有规格参数及可选值信息, json格式',
  `packing_list` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '包装清单',
  `after_service` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '售后服务',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`spu_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品spu详情表';

-- 商品sku和时间区间关联中间表
DROP TABLE IF EXISTS sys_goods_sku_service_time_slots;
CREATE TABLE `sys_goods_sku_service_time_slots`  (
  `sku_id` bigint(20) UNSIGNED NOT NULL,
  `time_slot_id` bigint(20) UNSIGNED NOT NULL,
  `stock` int(8) UNSIGNED NOT NULL COMMENT '库存',
  PRIMARY KEY (`sku_id`, `time_slot_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '【中间关联表】商品sku和时间区间';

-- 服务时间表
DROP TABLE IF EXISTS sys_goods_service_time;
CREATE TABLE `sys_goods_service_time`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL COMMENT '服务日期',
  `start_time` TIME NOT NULL COMMENT '时间段的开始时间',
  `end_time` TIME NOT NULL COMMENT '时间段的结束时间',
  `enable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否有效: 0 - 否, 1 - 是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品服务时间区间表';

-- 商品sku表
DROP TABLE IF EXISTS sys_goods_sku;
CREATE TABLE `sys_goods_sku`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spu_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品spu_id',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品标题',
  `images` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '商品的图片, 多个图片以\',\'分隔',
  `stock` int(8) UNSIGNED NOT NULL COMMENT '库存',
  `price` bigint(16) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售价格, 单位为分',
  `indexes` varchar(32) COLLATE utf8mb4_unicode_ci NULL COMMENT '特有规格属性在 spu 属性模版中的对应下标组合',
  `own_spec` varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT 'sku的特有规格参数键值对, json格式, 反序列化时请使用 linkedHasMap, 保证有序',
  `enable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否有效: 0 - 否, 1 - 是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `key_spu_id`(`spu_id`) USING BTREE
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品sku表';

-- 商品spu表
DROP TABLE IF EXISTS sys_goods_spu;
CREATE TABLE `sys_goods_spu`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `sub_title` varchar(255) COLLATE utf8mb4_unicode_ci NULL COMMENT '副标题, 一般是促销信息',
  `category_id_1` bigint(20) UNSIGNED NULL COMMENT '1级类目id',
  `category_id_2` bigint(20) UNSIGNED NULL COMMENT '2级类目id',
  `category_id_3` bigint(20) UNSIGNED NULL COMMENT '3级类目id',
  `category_ids`  varchar(1024) COLLATE utf8mb4_unicode_ci NULL COMMENT '类目id组合, json格式, 反序列化时请使用 linkedHasMap, 保证有序',
  `brand_id` bigint(20) UNSIGNED NOT NULL COMMENT '商品所属品牌id',
  `saleable` tinyint(1) UNSIGNED NOT NULL DEFAULT true COMMENT '是否上架: 0 - 否, 1 - 是',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '商品spu表';

-- 商品品牌表
DROP TABLE IF EXISTS sys_goods_brand;
CREATE TABLE `sys_goods_brand`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '品牌id',
  `title` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '品牌名称',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NULL COMMENT '品牌图片地址',
  `letter` char(1) COLLATE utf8mb4_unicode_ci NULL COMMENT '品牌的首字母',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '品牌表';

-- 商品分类表
DROP TABLE IF EXISTS sys_goods_category;
CREATE TABLE `sys_goods_category`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '类目id',
  `title` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类目名称',
  `parent_id` bigint(20) UNSIGNED NOT NULL COMMENT '父类目id, 顶级类目填0',
  `is_parent` tinyint(1) UNSIGNED NOT NULL COMMENT '是否为父节点: 0 - 否, 1 - 是',
  `sort` tinyint(2) UNSIGNED NOT NULL COMMENT '排序指数, 越小越靠前',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `key_parent_id`(`parent_id`) USING BTREE
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '分类表';

ALTER TABLE `sys_goods_category_brand_slots` ADD FOREIGN KEY (`category_id`) REFERENCES `sys_goods_category` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_category_brand_slots` ADD FOREIGN KEY (`brand_slot_id`) REFERENCES `sys_goods_brand` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_sku_service_time_slots` ADD FOREIGN KEY (`sku_id`) REFERENCES `sys_goods_sku` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_sku_service_time_slots` ADD FOREIGN KEY (`time_slot_id`) REFERENCES `sys_goods_service_time` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_sku` ADD FOREIGN KEY (`spu_id`) REFERENCES `sys_goods_spu` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_spec_group` ADD FOREIGN KEY (`category_id`) REFERENCES `sys_goods_category` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_spec_param` ADD FOREIGN KEY (`category_id`) REFERENCES `sys_goods_category` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_spec_param` ADD FOREIGN KEY (`group_id`) REFERENCES `sys_goods_spec_group` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_spu` ADD FOREIGN KEY (`brand_id`) REFERENCES `sys_goods_brand` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE `sys_goods_spu_detail` ADD FOREIGN KEY (`spu_id`) REFERENCES `sys_goods_spu` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;