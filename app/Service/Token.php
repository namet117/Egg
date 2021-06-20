<?php

namespace App\Service;

use App\Constants\ContextConst;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;

class Token
{
    const USER_KEY = 'user:id:';

    private $redis;

    private function getRedis(): Redis
    {
        if (!$this->redis) {
            $this->redis = ApplicationContext::getContainer()->get(Redis::class);
        }

        return $this->redis;
    }

    private function createKey(string $token): string
    {
        return self::USER_KEY.$token;
    }

    public function checkToken(string $token): bool
    {
        if (strlen($token) !== 64) {
            return false;
        }

        return $this->getRedis()->exists($this->createKey($token));
    }

    public function createToken(): string
    {
        list($usec, $sec) = explode(' ', microtime());

        return md5($sec.$usec).uniqid('', true);
    }

    public function setToken(string $token): void
    {
        Context::set(ContextConst::AUTH_TOKEN, $token);
    }

    public function getToken(): string
    {
        return Context::get(ContextConst::AUTH_TOKEN, '');
    }

    public function getTokenInfo(): array
    {
        $token = $this->getToken();
        if (!$info = Context::get($token)) {
            $info = unserialize($this->getRedis()->get($this->createKey($token))) ?: [];
            Context::set(ContextConst::AUTH_INFO, $info);
        }

        return $info;
    }

    public function setTokenInfo(array $data)
    {
        $token = $this->getToken();
        Context::set($token, $data);
    }

    public function saveTokenInfo(): bool
    {
        return $this->getRedis()->set($this->createKey($this->getToken()), serialize($this->getTokenInfo()), 1800);
    }
}
