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
        // SqlLog追加
        $name = $options['name'] ?? null;
        $label = $options['label'] ?? null;
        $this->sqlAccessor->insert(
            SqlManage::getSql(SqlManage::INSERT_LOG),
            [
                $name,
                $label,
                $sqlString,
                json_encode($params),
                $executionTime,
            ],
            [],
            true
        );

        // SqlSummery更新
        if ($name && $label) {
            $logs = $this->sqlAccessor->getList(
                sprintf(SqlManage::getSql(SqlManage::LIST_LOG), $this->summaryEach),
                [$name, $label],
                [],
                true
            );
            $slowestParams = '';
            $maxExecutionTime = 0;
            $minExecutionTime = PHP_INT_MAX;
            foreach($logs as $log) {
                $executionTime = $log['executionTime'];
                if ($executionTime > $maxExecutionTime) {
                    $maxExecutionTime = $executionTime;
                    $slowestParams = $log['params'];
                }
                if ($executionTime < $minExecutionTime) {
                    $minExecutionTime = $executionTime;
                }
            }

            $updated = $this->sqlAccessor->update(
                SqlManage::getSql(SqlManage::UPDATE_SUMMARY),
                [
                    $slowestParams,
                    $maxExecutionTime,
                    $minExecutionTime,
                ],
                [],
                true
            );
            if (!$updated) {
                $this->sqlAccessor->insert(
                    SqlManage::getSql(SqlManage::INSERT_SUMMARY),
                    [
                        $name,
                        $label,
                        $slowestParams,
                        $maxExecutionTime,
                        $minExecutionTime,
                    ],
                    [],
                    true
                );
            }
        }
    }
}