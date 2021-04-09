<?php

namespace Agp\TwoFactor\Service;

use PragmaRX\Google2FA\Google2FA;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;

class GoogleAuthenticationService
{
    /** Consulta se o usuario possui um
     * @param Model|object $usuario
     * @return object
     * @throws ValidationException
     */
    public function get($usuario)
    {
        return $this->request('get', null, '/google-authenticator/'.$usuario->getKey());
    }

    /** Realiza a creação do secrete key Google
     * @param Model|object $usuario
     * @param int $length
     * @return object
     * @throws ValidationException
     */
    public function create($usuario, $length = 64)
    {
        $data = [
            'adm_pessoa_id' => $usuario->getKey()
        ];

        return $this->request('post', $data, '/google-authenticator');
    }

    public function verify($usuario, $secret)
    {
        $data = [
            'adm_pessoa_id' => $usuario->getKey(),
            "secret" => $secret
        ];

        return $this->request('post', $data, '/google-authenticator/verify');
    }

    public function destroy($usuario, $secret)
    {
        $data = [
            'adm_pessoa_id' => $usuario->getKey(),
            "secret" => $secret
        ];

        return $this->request('delete', $data, '/google-authenticator/'.$usuario->getKey());
    }

    private function request($method, $data, $urlParams = '')
    {
        $url = config('config.api_agpadmin');
        if ($url == '')
            throw ValidationException::withMessages(['message' => 'Parametro "config.api_agpadmin" não informado.']);

        $headers = [
            'Content-type' => 'application/json',
            'Accept' => 'application/json',
            //'Authorization' => 'bearer '.auth()->getToken(),
        ];

        switch ($method){
            case 'post':
                $res = Http::withHeaders($headers)->post($url.$urlParams, $data);
                break;
            case 'put':
                $res = Http::withHeaders($headers)->put($url.$urlParams, $data);
                break;
            case 'patch':
                $res = Http::withHeaders($headers)->patch($url.$urlParams, $data);
                break;
            case 'delete':
                $res = Http::withHeaders($headers)->delete($url.$urlParams, $data);
                break;
            case 'get':
            default:
                $res = Http::withHeaders($headers)->get($url.$urlParams);
                break;
        }

        if (($res->status() == 200) || ($res->status() == 201)) {
            return $res->object();
        } else {
            $data = $res->json();
            if ($data && array_key_exists('errors', $data))
                throw ValidationException::withMessages($data['errors']);
            throw ValidationException::withMessages(['message' => $data['message']]);
        }
    }

}
