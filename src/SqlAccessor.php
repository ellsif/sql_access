<?php
namespace ellsif\sql_access;

/**
 * SQL実行管理用クラス
 */
class SqlAccessor
{
    protected $dbType = null;

    protected $pdo = null;

    protected $logger = null;

    protected $startTime = null;

    public function __construct(string $dsn, string $username = null, string $password = null, array $options = [])
    {
        $this->pdo = new \PDO($dsn, $username, $password, $options);
        if (strpos(strtolower($dsn), 'mysql') === 0) {
            $this->dbType = 'mysql';
        }
    }

    public function getDbType()
    {
        return $this->dbType;
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollback();
    }

    /**
     * ロガーを指定します。
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function createTable($sql, $options = null)
    {
        $stmt = $this->execute($sql, [], $options, true);
        return;
    }

    /**
     * 値を取得します。主にCOUNT()に利用します。
     */
    public function getValue($sql, $params, $options = null, $skipLog = false)
    {
        $stmt = $this->execute($sql, $params, $options, $skipLog);
        return $stmt->fetchColumn(\PDO::FETCH_NAMED);
    }

    /**
     * 1件取得します。
     */
    public function getSingle($sql, $params, $options = null, $skipLog = false)
    {
        $stmt = $this->execute($sql, $params, $options, $skipLog);
        return $stmt->fetch(\PDO::FETCH_NAMED);
    }

    /**
     * 対象データを配列で取得します。
     */
    public function getList($sql, $params, $options = null, $skipLog = false)
    {
        $stmt = $this->execute($sql, $params, $options, $skipLog);
        return $stmt->fetchAll(\PDO::FETCH_NAMED);
    }

    /**
     * データを登録します。
     */
    public function insert($sql, $params, $options = null, $skipLog = false)
    {
        $stmt = $this->execute($sql, $params, $options, $skipLog);
        return $stmt->rowCount();
    }

    protected function execute($sql, $params, $options, $skipLog)
    {
        $this->onStart();
        $stmt = $this->pdo->prepare($sql);
        if (is_array($params) && !empty($params)) {
            for ($idx = 0; $idx < count($params); $idx++) {
                if (!$stmt->bindValue($idx + 1, $params[$idx])) {
                    throw new \Exception("bindValue() failed idx:$idx, param: $params[$idx]");
                }
            }
        }
        if ($stmt->execute()) {
            $executionTime = $this->onEnd();
            if (!$skipLog && $this->logger) {
                $this->logger->log($sql, $params, $executionTime, $options);
            }
            return $stmt;
        } else {
            throw new \Exception(
                "SQL execution failed:" . implode(", ", $stmt->errorInfo())
            );
        }
    }

    protected function onStart()
    {
        $this->startTime = microtime(true);
    }

    protected function onEnd()
    {
        $executionTime = microtime(true) - $this->startTime;
        $executionTime = round($executionTime * 1000);
        return $executionTime;
    }
}