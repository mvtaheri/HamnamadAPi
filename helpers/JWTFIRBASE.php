<?php

use Slim\Interfaces\Http
    ;
class JWTFIRBASE{

    public function authorize(HeaderInterface $authHeader)
    {
        list($jwt) = sscanf($authHeader->toString(), 'Authorization: Bearer %s');
        if ($jwt) {
            try {
                /*
                 * decode the jwt using the key from config
                 */
                $secretKey = base64_decode($this->config->get('jwt')->get('key'));
                $this->token = JWT::decode($jwt, $secretKey, [$this->config->get('jwt')->get('algorithm')]);
                $this->isAuthorized = true;
                $this->response = Response::createMessage("10");
            } catch (Exception $e) {
                /*
                 * the token was not able to be decoded.
                 * this is likely because the signature was not able to be verified (tampered token)
                 */
                $this->isAuthorized = false;
                $this->response = Response::createMessage("03");
                $this->response["data"] = $jwt;
            }
        } else {
            /*
             * No token was able to be extracted from the authorization header
             */
            $this->isAuthorized = false;
            $this->response = Response::createMessage("01");
        }
    }
}