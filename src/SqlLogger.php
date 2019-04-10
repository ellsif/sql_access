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

        // テーブル作成 TODO mysql以外の対応
        if ($sqlAccessor->getDbType() === 'mysql') {
            $sqlAccessor->create(SqlManage::getSql(SqlManage::CREATE_LOG_TABLE_MYSQL), []);
            if ($sqlAccessor->getValue(SqlManage::getSql(SqlManage::CHECK_LOG_INDEX_MYSQL), [$sqlAccessor->getDbname()]) == 0) {
                $sqlAccessor->create(SqlManage::getSql(SqlManage::CREATE_LOG_INDEX_MYSQL), []);
            }
            $sqlAccessor->create(SqlManage::getSql(SqlManage::CREATE_SUMMARY_TABLE_MYSQL), []);
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