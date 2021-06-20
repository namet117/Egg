<?php


namespace App\Service;


use App\Model\UserOauth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Hyperf\Config\Annotation\Value;
use Hyperf\Utils\Arr;

class WechatMP
{
    /**
     * @Value("egg.mp.key")
     */
    private $key;

    /**
     * @Value("egg.mp.secret")
     */
    private $secret;

    public function code2User(string $code): array
    {
        $info = $this->code2Session($code);
        if (!$info) return [];
        $user = UserOauth::where(['openid' => $info['openid']])->first();
        if (!$user) return $info;
        $info['user_id'] = $user->id;
        if ($user->session_key !== $info['session_key']) {
            $user->session_key = $info['session_key'];
            $user->save();
        }

        return $info;
    }

    public function code2Session(string $code): array
    {
        $client = new Client();
        $base_url = 'https://api.weixin.qq.com/sns/jscode2session';
        $query = [
            'appid' => $this->key,
            'secret' => $this->secret,
            'js_code' => $code,
            'grant_type' => 'authorization_code',
        ];
        try {
            $response = $client->get($base_url, compact('query'));
            $body = (string)$response->getBody();
            $json = json_decode($body, true);
            if ($body && $json && ($json['errcode'] == 0)) {
                return Arr::only($json, ['openid', 'unionid', 'session_key']);
            } else {
                // FIXME 上报body到日志系统
            }
        } catch (RequestException $e) {
            // FIXME 上报到日志系统
        }

        return [];
    }
}
