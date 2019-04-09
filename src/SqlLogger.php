<?php
namespace ellsif\sql_access;

/**
 * ロガークラス。DBに対してログを出力します。
 */
class SqlLogger
{
    protected $summaryEach = 50;

    protected $sqlAccessor = null;

    public function __construct(SqlAccessor $sqlAccessor)
    {
        $this->sqlAccessor = $sqlAccessor;

        // テーブル作成(MySQLのみ)
        if ($sqlAccessor->getDbType() === 'mysql') {
            $sqlAccessor->createTable(SqlManage::getSql(SqlManage::CREATE_LOG_TABLE_MYSQL), []);
            $sqlAccessor->createTable(SqlManage::getSql(SqlManage::CREATE_SUMMARY_TABLE_MYSQL), []);
        }
        $sqlAccessor->setLogger($this);
    }

    /**
     * サマリー生成に使うログの件数を指定します。
     * デフォルトでは直近の50件からサマリーを生成します。
     */
    public function setSummaryEach($summaryEach)
    {
        $this->summaryEach = $summaryEach;
    }

    /**
     * ログを出力します。
     */
    public function log($sqlString, $params, $executionTime, $options)
    {
        $this->sqlAccessor->insert(
            SqlManage::getSql(SqlManage::INSERT_LOG),
            [
                $options['name'] ?? null,
                $options['label'] ?? null,
                $sqlString,
                json_encode($params),
                $executionTime,
            ],
            [],
            true
        );
    }
}