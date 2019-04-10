<?php
namespace ellsif\sql_access;
use ellsif\sql_manager\SqlManageBase;

class SqlManage extends SqlManageBase
{
	public const CREATE_LOG_TABLE_MYSQL = "Create SQL log table for MySQL";
	public const CREATE_SUMMARY_TABLE_MYSQL = "Create LogSummary table for MySQL";
	public const INSERT_LOG = "Insert log";
	public const INSERT_SUMMARY = "Insert LogSummary";
	public const CREATER_EXAMPLE_CUSTOMER = "Create customer for example";
	public const INSERT_EXAMPLE_CUSTOMER = "Insert Customer example";
	public const CREATE_LOG_INDEX_MYSQL = "Create Index for SqlLog";
	public const CHECK_LOG_INDEX_MYSQL = "check SqlLogIndex exists";
	public const UPDATE_SUMMARY = "Update SqlLogSummary";
	public const LIST_LOG = "Get logs by name and label";

    public static function getSettings()
    {
        return
array (
  0 => 
  array (
    'name' => 'CREATE_LOG_TABLE_MYSQL',
    'label' => 'Create SQL log table for MySQL',
    'sql' => 'CREATE TABLE IF NOT EXISTS 
SqlLog (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(512),
  label VARCHAR(512),
  sqlString TEXT NOT NULL,
  params TEXT,
  executionTime INT NOT NULL,
  created DATETIME,
  PRIMARY KEY (id)
)',
    'note' => '',
  ),
  1 => 
  array (
    'name' => 'CREATE_SUMMARY_TABLE_MYSQL',
    'label' => 'Create LogSummary table for MySQL',
    'sql' => 'CREATE TABLE IF NOT EXISTS 
SqlLogSummary (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(512),
  label VARCHAR(512),
  slowestParams TEXT,
  maxExecutionTime INT NOT NULL,
  minExecutionTime INT NOT NULL,
  created DATETIME,
  updated DATETIME,
  PRIMARY KEY (id)
)',
    'note' => '',
  ),
  2 => 
  array (
    'name' => 'INSERT_LOG',
    'label' => 'Insert log',
    'sql' => 'INSERT INTO SqlLog (name, label, sqlString, params, executionTime, created) VALUES (?,?,?,?,?,NOW())',
    'note' => '',
  ),
  3 => 
  array (
    'name' => 'INSERT_SUMMARY',
    'label' => 'Insert LogSummary',
    'sql' => 'INSERT INTO SqlLogSummary
(name, label, slowestParams, maxExecutionTime, minExecutionTime, created, updated)
VALUES
(?, ?, ?, ?, ?, NOW(), NOW())',
    'note' => '',
  ),
  4 => 
  array (
    'name' => 'CREATER_EXAMPLE_CUSTOMER',
    'label' => 'Create customer for example',
    'sql' => 'CREATE TABLE IF NOT EXISTS 
ExampleCustomer (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(512),
  address VARCHAR(512),
  email VARCHAR(512),
  created DATETIME,
  updated DATETIME,
  PRIMARY KEY (id)
)',
    'note' => '',
  ),
  5 => 
  array (
    'name' => 'INSERT_EXAMPLE_CUSTOMER',
    'label' => 'Insert Customer example',
    'sql' => 'INSERT INTO ExampleCustomer (name, address, email, created, updated) VALUES (?, ?, ?, NOW(), NOW())',
    'note' => '',
  ),
  6 => 
  array (
    'name' => 'CREATE_LOG_INDEX_MYSQL',
    'label' => 'Create Index for SqlLog',
    'sql' => 'ALTER TABLE SqlLog ADD INDEX SqlLogIndex (name, label);
',
    'note' => '',
  ),
  7 => 
  array (
    'name' => 'CHECK_LOG_INDEX_MYSQL',
    'label' => 'check SqlLogIndex exists',
    'sql' => 'SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME=\'SqlLog\' AND INDEX_NAME=\'SqlLogIndex\'',
    'note' => '',
  ),
  8 => 
  array (
    'name' => 'UPDATE_SUMMARY',
    'label' => 'Update SqlLogSummary',
    'sql' => 'UPDATE SqlLogSummary SET slowestParams = ?, maxExecutionTime = ?, minExecutionTime =?, updated = NOW()',
    'note' => '',
  ),
  9 => 
  array (
    'name' => 'LIST_LOG',
    'label' => 'Get logs by name and label',
    'sql' => 'SELECT * FROM SqlLog WHERE name = ? AND label = ? ORDER BY created DESC LIMIT %d',
    'note' => '',
  ),
);
    }
}