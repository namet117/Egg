<?php

declare(strict_types=1);

namespace App\Controller;

use _HumbugBox2acd634d137b\Nette\Neon\Exception;
use App\Constants\StatusConst;
use Hyperf\Di\Annotation\Inject;

class AuthController extends AbstractController
{
    /**
     * @Inject
     *
     * @var \App\Service\Auth
     */
    private $auth;

    /**
     * @Inject
     *
     * @var \App\Service\UserService
     */
    private $userService;

    /**
     * @Inject
     *
     * @var \App\Service\WechatMP
     */
    private $mpService;

    public function status()
    {
        return $this->auth->isLogin()
            ? $this->success(['id' => $this->auth->id()], '已登录')
            : $this->failed('未登录');
    }

    public function loginByWxCode()
    {
        $code = $this->request->post('code', '');
        if (!$code) {
            return $this->failed('参数错误');
        }
        if ($this->auth->isLogin()) {
            return $this->success(['id' => $this->auth->id()]);
        }
        $data = $this->mpService->code2User($code);
        if (!$data) {
            return $this->failed('读取用户ID失败', StatusConst::LOGIN_FAIL);
        }
        $user = ['name' => $data['openid']];
        if (!$data['user_id'] && !$this->userService->createNewAccount($user, $data)) {
            return $this->failed('创建用户id失败', StatusConst::LOGIN_FAIL);
        }
        $this->auth->login($data['user_id']);
        return $this->success(['id' => $data['user_id']]);
    }

    public function loginByPassword()
    {
        $name = str_replace(' ', '', $this->request->post('name'));
        $password = str_replace(' ', '', $this->request->post('password'));
        if (empty($name) || empty($password)) {
            return $this->failed('请输入账号或密码');
        }
        $id = $this->userService->tryLogin($name, $password);

        return $this->success(compact('id'), '登录成功');
    }

    public function signUpByPassword()
    {
        $validator = $this->validationFactory->make(
            $this->request->post(),
            [
                'name' => 'required|between:6,20|regex:/[a-z0-9_]/i|unique:users,name',
                'password' => 'required|between:6,20|regex:/[a-zA-Z0-9_]/',
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
                'new' => 'required|between:6,20|regex:/[a-zA-Z0-9_]/',
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
