<?php
namespace ellsif\sql_access;

/**
 * SQL実行管理用クラス
 */
class SqlAccessor
{
    protected $dbType = null;

    protected $dsnOptions = [];

    protected $pdo = null;

    protected $logger = null;

    protected $startTime = null;

    public function __construct(string $dsn, string $username = null, string $password = null, array $options = [])
    {
        $exp = explode(':', $dsn);
        $this->dbType = strtolower($exp[0]);
        if (count($exp) > 1) {
            $dsnOptions = explode(';', $exp[1]);
            foreach($dsnOptions as $dsnOption) {
                $dsnOption = explode('=', $dsnOption);
                $this->dsnOptions[strtolower($dsnOption[0])] = $dsnOption[1];
            }
        }
        $this->pdo = new \PDO($dsn, $username, $password, $options);
    }

    public function getDbType()
    {
        return $this->dbType;
    }

    public function getDbname()
    {
        if (isset($this->dsnOptions['dbname'])) {
            return $this->dsnOptions['dbname'];
        }
        throw new \Exception('dbname not found');
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

    public function create($sql, $options = null)
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
        return $stmt->fetchColumn();
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