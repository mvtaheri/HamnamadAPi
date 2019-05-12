<?php

namespace App\Helpers;


use Carbon\Carbon;
use Exception;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

/**
 * Class JWT
 * @package App\Helpers
 */
class JWT
{
    /**
     * @param null $data
     * @param null $expire
     * @return string
     */
    public static function make($data = null, $expire = null)
    {
        $signature = new Sha256();
        $tokenExpireTime = settings('expire')['jwt'];

        $token = (new Builder())->setIssuer('fundbaz')
            ->setId('4f1g23a12aa' . uniqid(), true)
            ->setIssuedAt(time())
            ->setNotBefore(time())
            ->setExpiration($life = $expire ?? $tokenExpireTime)// Configures the expiration time of the token (nbf claim)
            ->set('data', $data)
            ->sign($signature, 'supersecretkeyyoushouldnotcommittogithub')
            ->getToken();

        return (string)$token;
    }

    /**
     * @param $token
     * @return bool|string
     */
    public static function validate($token)
    {
        try {
            $token = str_replace('Bearer ', '', $token);
            $token = (new Parser())->parse($token);

            $data = new ValidationData();
            $data->setIssuer($token->getClaim('iss'));
            $data->setId($token->getClaim('jti'));
            $data->setCurrentTime(Carbon::now()->timestamp);

            return $token->validate($data);
        } catch (Exception $e) {
            return "Validate error, {$e->getMessage()}";
        }

    }

    /**
     * @param $token
     * @return mixed|string
     */
    public static function parse($token)
    {
        $token = str_replace('Bearer ', '', $token);
        try {
            $token = (new Parser())->parse($token);

            return $token->getClaim('data');
        } catch (Exception $e) {
            return "Parse error, {$e->getMessage()}";
        }
    }

    /**
     * @param $token
     * @return string
     */
    public static function refresh($token)
    {
        $token=str_replace('Bearer','',$token);
        $token = (new Parser())->parse($token);
        $signature = new Sha256();

        $refresh = (new Builder())->setIssuer($token->getClaim('iss'))
            ->setId($token->getClaim('jti'), true)
            ->setIssuedAt($token->getClaim('iat'))
            ->setNotBefore($token->getClaim('nbf'))
            ->setExpiration(settings('expire')['jwt'])
            ->set('data', $token->getClaim('data'))
            ->sign($signature, 'supersecretkeyyoushouldnotcommittogithub')
            ->getToken();

        return (string)$refresh;
    }
}