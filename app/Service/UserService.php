<?php

namespace App\Service;


use App\Exception\EggException;
use App\Model\User;
use App\Model\UserOauth;

class UserService
{
    public function tryLogin(string $name, string $password): int
    {
        $user = User::where('name', $name)->first();
        if (!$user) throw new EggException('账号不存在');
        if (!$this->checkPassword($password, $user->password)) {
            throw new EggException('账号和密码不匹配');
        }
        $this->loginById($user->id);

        return $user->id;
    }

    public function createNewAccount(array $data, bool $login = true): int
    {
        $data['custom'] = 1;
        $data['password'] = $this->encryptPassword($data['password']);
        $user = User::create($data);
        if (!$user->save()) throw new EggException('创建用户失败，请重试');
        $login && $this->loginById($user->id);

        return $user->id;
    }

    public function loginById(int $id): bool
    {
        $auth = new Auth;
        $auth->login($id);
        $info = $auth->getTokenInfo();
        if ($info['mp']) {
            UserOauth::updateOrCreate(
                [
                    'user_id' => $id,
                    'openid' => $info['mp']['openid'],
                ],
                [
                    'unionid' => $info['mp']['unionid'],
                    'session_key' => $info['mp']['session_key'],
                ]
            );
        }

        return true;
    }

    public function encryptPassword(string $password): string
    {
        $password = password_hash($password, PASSWORD_BCRYPT);
        if (!$password) throw new EggException('生成加密密码失败，请重试');

        return $password;
    }

    public function checkPassword(string $text, string $encrypted): bool
    {
        return password_verify($text, $encrypted);
    }

    public function resetPassword(int $id, string $password): bool
    {
        $user = User::find($id);
        if (!$user) throw new EggException('不存在的用户');
        $user->password = $this->encryptPassword($password);

        return $user->save();
    }
}
