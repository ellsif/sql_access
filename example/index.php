<?php
/**
 * SqlAccessor利用サンプル
 */
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
use ellsif\sql_access\SqlAccessor;
use ellsif\sql_access\SqlLogger;
use ellsif\sql_access\SqlManage;

$sqlAccessor = new SqlAccessor(
    'mysql:host=127.0.0.1;port=13316;dbname=SqlLog;charset=utf8',
    'root',
    'root'
);
$sqlLogger = new SqlLogger($sqlAccessor);

// ExampleCustomerテーブル作成
$sqlAccessor->create(SqlManage::getSql(SqlManage::CREATER_EXAMPLE_CUSTOMER), []);

// カスタマー保存
$sqlAccessor->insert(
    SqlManage::getSql(SqlManage::INSERT_EXAMPLE_CUSTOMER),
    [
        'Test Taro',
        '000-0000 Tokyo',
        'test@example.com',
    ],
    SqlManage::getSetting(SqlManage::INSERT_EXAMPLE_CUSTOMER)
);
