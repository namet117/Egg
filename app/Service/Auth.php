<?php

namespace App\Service;

use App\Constants\ContextConst;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Context;

class Auth
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

    public function checkToken(string $token = ''): bool
    {
        $token = $token ?: $this->getToken();
        if (strlen($token) !== 55) {
            return false;
        }

        return $this->getRedis()->exists($this->createKey($token));
    }

    public function createToken(bool $save = true): string
    {
        list($usec, $sec) = explode(' ', microtime());
        $token = sha1($sec . $usec) . uniqid('', true);
        $save && $this->setToken($token);

        return $token;
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

    public function setTokenInfo(array $data, bool $append = true)
    {
        $token = $this->getToken();
        if ($append) {
            $old = $this->getTokenInfo();
            $data = array_merge($data, $old);
        }
        Context::set($token, $data);
    }

    public function saveTokenInfo(): bool
    {
        if (!$this->getToken()) {
            return false;
        }
        return $this->getRedis()->set($this->createKey($this->getToken()), serialize($this->getTokenInfo()), 1800);
    }

    public function login(int $id): void
    {
        $this->setTokenInfo(['user_id' => $id]);
    }

    public function id(): int
    {
        $info = $this->getTokenInfo();

        return $info['user_id'] ?? 0;
    }

    public function isLogin(): bool
    {
        return $this->id() !== 0;
    }
}
