<?php
/**
 * @PHP       Version >= 8.0
 * @copyright ©2024 Maatify.dev
 * @author    Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since     2024-07-20 8:50 AM
 * @link      https://www.maatify.dev Maatify.com
 * @link      https://github.com/Maatify/QueueManager  view project on GitHub
 * @Maatify   Redis :: QueueManager
 */

namespace Maatify\QueueManager;

use App\Assist\Redis\RedisQueue;
use Maatify\Logger\Logger;

class RedisQueueHandler
{

    private static self $instance;

    public static function obj(): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private ?bool $redis_active = null;

    private string $cache_key = 'portal:';

    public function __construct()
    {
        if ($this->redis_active === null) {
            if (extension_loaded('redis')) {
                $this->redis_active = true;
            } else {
                $this->redis_active = false;
            }
        }
    }

    public function __destruct()
    {
        if($this->redis_active && $this->RedisCheckQueue()) {
            RedisQueue::obj()->Set($this->cache_key,false);
        }
    }

    public function RemoveQueueByCacheKey(string $cache_key): void
    {
        $this->cache_key = $cache_key;
        if($this->redis_active && $this->RedisCheckQueue()) {
            RedisQueue::obj()->Set($this->cache_key,false);
        }
    }

    public function Status(): bool
    {
        return $this->redis_active;
    }

    private function RedisCheckQueue():bool
    {
        if($this->redis_active) {
            $result = RedisQueue::obj()->Get($this->cache_key);
            if (! empty($result)) {
                return true;
            }
        }
        return false;
    }

    private function RedisSetInQueue():void
    {
        if($this->redis_active){
            if(!$this->RedisCheckQueue()) {
                RedisQueue::obj()->Set($this->cache_key, true);
            }else{
                sleep(10);
                $this->RedisSetInQueue();
            }
        }
    }

    private function RedisSetCronInQueue():void
    {
        if($this->redis_active && !$this->RedisCheckQueue()){
            RedisQueue::obj()->Set($this->cache_key,true);
        }else{
            Logger::RecordLog('script die due active redis cron with cache key' . $this->cache_key, 'queue');
            die();
        }
    }

    public function SetEmailQueue(): void
    {
        $this->cache_key = 'email';
        $this->RedisSetCronInQueue();
    }

    public function SetFcmQueue(): void
    {
        $this->cache_key = 'fcm';
        $this->RedisSetCronInQueue();
    }

    public function SetSmsQueue(): void
    {
        $this->cache_key = 'sms';
        $this->RedisSetCronInQueue();
    }

    public function SetOrderQueue(): void
    {
        $this->cache_key = 'order';
        $this->RedisSetInQueue();
    }

    public function SetPaymentQueue(): void
    {
        $this->cache_key = 'payment';
        $this->RedisSetInQueue();
    }

    public function SetQueueByKey(string $cache_key): void
    {
        $this->cache_key = $cache_key;
        $this->RedisSetInQueue();
    }

    public function SetCronQueueByKey(string $cache_key): void
    {
        $this->cache_key = $cache_key;
        $this->RedisSetCronInQueue();
    }
}