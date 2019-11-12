<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii2lab\db\domain\log;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\VarDumper;
use yii\log\DbTarget as DbTargetBase;
use yii\log\FileTarget;
use yii\log\LogRuntimeException;

class DbTarget extends DbTargetBase
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbTarget object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $db = 'db';
    /**
     * @var string name of the DB table to store cache content. Defaults to "log".
     */
    public $logTable = '{{%log}}';

	public $logFile = 'db.log';
    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     * @throws InvalidConfigException if [[db]] is invalid.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }


    public function export()
    {
        if ($this->db->getTransaction()) {
            // create new database connection, if there is an open transaction
            // to ensure insert statement is not affected by a rollback
            $this->db = clone $this->db;
        }
        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "INSERT INTO $tableName ([[level]], [[category]], [[log_time]], [[prefix]], [[message]])
                VALUES (:level, :category, :log_time, :prefix, :message)";
        $command = $this->db->createCommand($sql);
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            if ($command->bindValues([
                    ':level' => $level,
                    ':category' => $category,
                    ':log_time' => $timestamp,
                    ':prefix' => $this->getMessagePrefix($message),
                    ':message' => $text,
                ])->execute() > 0) {
				$this->exportFile($message);
                continue;
            }
            throw new LogRuntimeException('Unable to export log through database!');
        }
    }

	private function exportFile($message){
		$fileTarget = new FileTarget();
		list($text, $level, $category, $timestamp) = $message;
		$timestamp = date('Y-m-d H:i:s', $timestamp);
		$fileTarget->messages = [["level: {$level}; category: {$category}; timestamp: {$timestamp}:query: $text: prefix: {$this->getMessagePrefix($message) }"]];
		$fileTarget->logFile = $this->logFile;
		$fileTarget->export();
	}
}
