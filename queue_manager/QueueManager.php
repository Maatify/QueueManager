<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-20 8:50 AM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/QueueManager  view project on GitHub
 * @Maatify   DB :: QueueManager
 */

namespace Maatify\QueueManager;

use App\DB\DBS\DbConnector;
use JetBrains\PhpStorm\NoReturn;
use Maatify\Logger\Logger;

class QueueManager extends DbConnector
{
    const TABLE_NAME                 = 'queue_manager';
    const TABLE_ALIAS                = '';
    const IDENTIFY_TABLE_ID_COL_NAME = 'queue_id';
    const LOGGER_TYPE                = self::TABLE_NAME;
    const LOGGER_SUB_TYPE            = '';
    const Cols                       =
        [
            self::IDENTIFY_TABLE_ID_COL_NAME => 1,
            'name'                           => 1,
            'timestamp'                      => 0,
        ];

    protected string $tableName = self::TABLE_NAME;
    protected string $tableAlias = self::TABLE_ALIAS;
    protected string $identify_table_id_col_name = self::IDENTIFY_TABLE_ID_COL_NAME;
    protected array $cols = self::Cols;
    protected int $time;
    protected int $timeout = 59;

    protected RedisQueueHandler $redisQueue;

    private static self $instance;
    protected int $queue_id;
    protected string $redis_queue_cache_key;
    protected bool $redis_queue_status;

    public static function obj(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        parent::__construct();
        $this->redisQueue = RedisQueueHandler::obj();
        $this->redis_queue_status = $this->redisQueue->Status();
    }

    public function __destruct()
    {
        $this->Stop();
    }

    public function Email(): void
    {
        $this->queue_id = 1;
        $this->redis_queue_cache_key = 'email';
        $this->CronStart();
    }

    public function Sms(): void
    {
        $this->queue_id = 2;
        $this->redis_queue_cache_key = 'sms';
        $this->CronStart();
    }

    public function Fcm(): void
    {
        $this->queue_id = 3;
        $this->redis_queue_cache_key = 'fcm';
        $this->CronStart();
    }

    public function Payment(): void
    {
        $this->queue_id = 4;
        $this->redis_queue_cache_key = 'payment';
        $this->Start();
    }

    public function Order(): void
    {
        $this->queue_id = 5;
        $this->redis_queue_cache_key = 'order';
        $this->Start();
    }

    public function TelegramBotAdmin(): void
    {
        $this->queue_id = 6;
        $this->redis_queue_cache_key = 'telegram_bot_admin';
        $this->CronStart();
    }

    public function TelegramBotCustomer(): void
    {
        $this->queue_id = 7;
        $this->redis_queue_cache_key = 'telegram_bot_customer';
        $this->CronStart();
    }

    public function TelegramBotSubscriber(): void
    {
        $this->queue_id = 8;
        $this->redis_queue_cache_key = 'telegram_bot_subscriber';
        $this->CronStart();
    }

    private function Stop(): void
    {
        if ($this->redis_queue_status) {
            $this->redisQueue->RemoveQueueByCacheKey($this->redis_queue_cache_key);
        } else {
            $this->time = time() - 86400; // = 86400 = 24 * 60 * 60
            $this->QueueAction();
        }
    }

    private function Start(): void
    {
        if ($this->redis_queue_status) {
            $this->redisQueue->SetQueueByKey($this->redis_queue_cache_key);
        } else {
            if ($this->CurrentQueue() < time() - $this->timeout) {
                $this->time = time();
                $this->QueueAction();
            } else {
                sleep(10);
                $this->Start();
            }
        }
    }

    protected function CronStart(): void
    {
        if ($this->redis_queue_status) {
            $this->redisQueue->SetCronQueueByKey($this->redis_queue_cache_key);
        } else {
            if ($this->CurrentQueue() < time() - $this->timeout) {
                $this->time = time();
                $this->QueueAction();
            } else {
                Logger::RecordLog('script die due active queue in database mode with type' . $this->redis_queue_cache_key, 'queue');
                die();
            }
        }

    }

    private function QueueAction(): void
    {
        $this->Edit(['timestamp' => $this->time], "`$this->identify_table_id_col_name` = ?", [$this->queue_id]);
    }

    private function CurrentQueue(): int
    {
        return (int)$this->ColThisTable('timestamp', "`$this->identify_table_id_col_name` = ?", [$this->queue_id]);
    }
}