<?php

namespace App\Containers\Authentication\Actions;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Request;
use Route;

/**
 * Class ProxyApiLoginAction.
 *
 * @author Mahmoud Zalt <mahmoud@zalt.me>
 */
class ProxyApiLoginAction // extends Action
{

    /**
     * @string
     */
    CONST AUTH_ROUTE = '/v1/oauth/token';

    /**
     * @var  \GuzzleHttp\Client
     */
    private $httpCient;

    /**
     * ProxyApiLoginAction constructor.
     *
     * @param \GuzzleHttp\Client $httpCient
     */
    public function __construct(Client $httpCient)
    {
        $this->httpCient = $httpCient;
    }

    /**
     * @param $email
     * @param $password
     * @param $client
     *
     * @return  array
     */
    public function run($email, $password, $client)
    {
        // load the corresponding credentials of my trusted client.
        switch ($client) {
            case 'AdminWeb':
                $clientId = env('CLIENT_WEB_ADMIN_ID');
                $clientSecret = env('CLIENT_WEB_ADMIN_SECRET');
                break;
            case 'ClientWeb':
                // ...
                $clientId = null;
                $clientSecret = null;
                break;
            case 'ClientMobile':
                // ...
                $clientId = null;
                $clientSecret = null;
                break;
        }

        $authFullApiUrl = Config::get('apiato.api.url') . self::AUTH_ROUTE;

        $data = [
            'username'      => $email,
            'password'      => $password,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'grant_type'    => 'password',
            'scope'         => '',
        ];

        $request = Request::create($authFullApiUrl, 'POST', $data);

        $response = App::handle($request);

        // response content as Array
        return \GuzzleHttp\json_decode($response->getContent(), true);
    }
}
