<?php
/**
 * CBAPIClerk 0.3.0
 * Copyright (C) 2021 Dmitry Shumilin
 * 
 * MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Infernusophiuchus\CBAPIClerk;

use Infernusophiuchus\CBAPIClerk\Exceptions\HandleException;

class Handle
{

    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';

    protected $url;
    protected $login;
    protected $key;

    public function __construct(string $url, string $login, string $key)
    {
        
        foreach (['url', 'login', 'key'] as $i => $arg) {

            if (empty($$arg)) throw new HandleException(
                'The '.$arg.' cannot be empty.',
                ($i + 1) * -1
            );

            $this->$arg = $$arg;

        }

        if (substr($this->url, -1) !== '/') $this->url .= '/';

        if (substr($this->url, 0, 4) !== 'http') {

            switch (substr($this->url, 0, 2)) {

                case ':/':
                    $this->url = 'http'.$this->url;
                    break;

                case '//':
                    $this->url = 'http:'.$this->url;
                    break;

                default:
                    $this->url = 'http://'.$this->url;
                    break;

            }

        }

    }

    /**
     * Send command to the server and return the answer from it.
     * 
     * @param string $url
     * @param array $command
     * 
     * @return array
     * 
     * @throws Infernusophiuchus\CBAPIClerk\Exceptions\HandleException
     */
    protected function command(string $url, array $command) : array
    {

        if (empty($url)) throw new HandleException(
            'The url cannot be empty.',
            -1
        );

        $command = json_encode($command);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $command);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-type: application/json',
            'Content-length: '.strlen($command)
        ]);

        $answer = curl_exec($ch);

        $result = json_decode(
            $answer,
            true
        );

        if (is_array($result)) return $result;
        else throw new HandleException(
            HandleException::BAD_ANSWER_MESSAGE.' "'.$answer.'"',
            HandleException::BAD_ANSWER_CODE
        );

    }

    /**
     * Authenticate on the server.
     * 
     * @return string
     * 
     * @throws Infernusophiuchus\CBAPIClerk\Exceptions\HandleException
     */
    protected function auth() : string
    {

        $request = $this->command(
            $this->url.'api/auth/request',
            [
                'v' => '1.0',
                'login' => $this->login,
                'life_time' => 60
            ]
        );

        if ($request['code'] === 0) {

            $auth = $this->command(
                $this->url.'api/auth/auth',
                [
                    'v' => '1.0',
                    'login' => $this->login,
                    'hash' => md5($request['salt'].$this->key)
                ]
            );

            if ($auth['code'] === 0) return $auth['access_id'];
            else throw new HandleException(
                HandleException::AUTH_FAILURE_MESSAGE.
                ' Answer code: '.$auth['code'].
                ', answer message: "'.$auth['message'].'"',
                HandleException::AUTH_FAILURE_CODE
            );

        } else throw new HandleException(
            HandleException::REQUEST_FAILURE_MESSAGE.
            ' Answer code: '.$request['code'].
            ', answer message: "'.$request['message'].'"',
            HandleException::REQUEST_FAILURE_CODE
        );

    }

    /**
     * Perform the action with the data on the server.
     * 
     * @param string $action
     * @param array $command
     * 
     * @return array
     * 
     * @throws Infernusophiuchus\CBAPIClerk\Exceptions\HandleException
     */
    public function dataCrud(string $action, array $command) : array
    {

        if ($action === self::CREATE ||
            $action === self::READ ||
            $action === self::UPDATE ||
            $action === self::DELETE) {

            $command['access_id'] = $this->auth();

            return $this->command(
                $this->url.'api/data/'.$action,
                $command
            );

        } else throw new HandleException(
            HandleException::INVALID_ACTION_MESSAGE,
            HandleException::INVALID_ACTION_CODE
        );

    }

}
