<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Auth;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;

class AuthController extends AbstractController
{
    /**
     * @Inject
     * @var \App\Service\Auth
     */
    private $auth;

    /**
     * @Inject
     * @var \App\Service\UserService
     */
    private $userService;

    /**
     * @Inject
     * @var \App\Service\WechatMP
     */
    private $mpService;

    public function init()
    {
        $code = $this->request->post('code', '');
        if (!$code) return $this->failed('参数错误');
        $token = '';
        if (!$this->auth->getToken() || !$this->auth->checkToken()) {
            $token = $this->auth->createToken();
        }
        $data = $this->mpService->code2User($code);
        if (!$data) {
            return $this->failed('读取用户ID失败');
        }
        $this->auth->setTokenInfo(['mp' => $data]);
        if ($data['user_id'] && !$this->auth->isLogin()) {
            $this->auth->login($data['user_id']);
        }

        return $this->success(compact('token'));
    }

    public function login()
    {
        $name = str_replace(' ', '', $this->request->post('name'));
        $password = str_replace(' ', '', $this->request->post('password'));
        if (empty($name) || empty($password)) {
            return $this->failed('请输入账号或密码');
        }
        $id = $this->userService->tryLogin($name, $password);

        return $this->success(compact('id'), '登录成功');
    }

    public function signUp()
    {
        $validator = $this->validationFactory->make(
            $this->request->post(),
            [
                'name' => 'required|between:6,20|regex:/[a-z0-9_]/i|unique:users,name',
                'password' => 'required|between:6,20|regex:/[a-zA-Z0-9_]/'
            ],
            [
                'name.between' => '用户名长度6-20个字符',
                'name.regex' => '用户名仅可由字母、数字和下划线组成',
                'password.between' => '密码长度6-20个字符',
                'password.regex' => '密码仅可使用字母、数字和下划线',
            ],
            [
                'name' => '用户名',
                'password' => '密码',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $id = $this->userService->createNewAccount($validator->validated());

        return $this->success(compact('id'), '注册成功');
    }

    public function setPassword()
    {
        $validator = $this->validationFactory->make(
            $this->request->post(),
            [
                'old' => 'between:6,20',
                'new' => 'required|between:6,20|regex:/[a-zA-Z0-9_]/'
            ],
            [
                'old.between' => '原密码长度6-20个字符',
                'new.between' => '新密码长度6-20个字符',
                'new.regex' => '新密码仅可使用字母、数字和下划线',
            ],
            [
                'old' => '原密码',
                'new' => '新密码',
            ]
        );
        if ($validator->fails()) {
            return $this->failed($validator->errors()->first());
        }
        $data = $validator->validated();
        if ($this->userService->resetPassword($this->auth->id(), $data['new'])) {
            return $this->success([], '修改成功');
        }

        return $this->failed('修改失败');
    }
}
