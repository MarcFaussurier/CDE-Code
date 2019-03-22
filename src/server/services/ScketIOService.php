<?php
/**
 * Created by PhpStorm.
 * User: marcfsr
 * Date: 28/02/2019
 * Time: 12:02
 */


namespace CloudsDotEarth\App\Services;

use CloudsDotEarth\App\Models\Grade;
use CloudsDotEarth\App\Models\Group;
use CloudsDotEarth\App\Models\User;
use CloudsDotEarth\Bundles\Core\ControllerStack;
use CloudsDotEarth\Bundles\Core\Core;
use CloudsDotEarth\Bundles\Core\Interfaces\ServiceInterface;
use CloudsDotEarth\Bundles\Core\RequestHandler;
use CloudsDotEarth\Bundles\Core\Service;
use CloudsDotEarth\Bundles\core\ViewCompiler;
use Ellipse\Dispatcher;
use Jasny\HttpMessage\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Workerman\WebServer;
use PHPSocketIO\SocketIO;

class ScketIOService extends Service implements ServiceInterface {

    public $defaultPort = 8080;

    /**
     * @var SocketIO
     */
    public $service;

    public function register(): void {
        $this->server = new SocketIO(2020);
        $this->server->on('connection', function($socket){
            $socket->addedUser = false;
            // when the client emits 'new message', this listens and executes
            $socket->on('new message', function ($data)use($socket){
                // we tell the client to execute 'new message'
                $socket->broadcast->emit('new message', array(
                    'username'=> $socket->username,
                    'message'=> $data
                ));
            });
            // when the client emits 'add user', this listens and executes
            $socket->on('add user', function ($username) use($socket){
                global $usernames, $numUsers;
                // we store the username in the socket session for this client
                $socket->username = $username;
                // add the client's username to the global list
                $usernames[$username] = $username;
                ++$numUsers;
                $socket->addedUser = true;
                $socket->emit('login', array(
                    'numUsers' => $numUsers
                ));
                // echo globally (all clients) that a person has connected
                $socket->broadcast->emit('user joined', array(
                    'username' => $socket->username,
                    'numUsers' => $numUsers
                ));
            });
            // when the client emits 'typing', we broadcast it to others
            $socket->on('typing', function () use($socket) {
                $socket->broadcast->emit('typing', array(
                    'username' => $socket->username
                ));
            });
            // when the client emits 'stop typing', we broadcast it to others
            $socket->on('stop typing', function () use($socket) {
                $socket->broadcast->emit('stop typing', array(
                    'username' => $socket->username
                ));
            });
            // when the user disconnects.. perform this
            $socket->on('disconnect', function () use($socket) {
                global $usernames, $numUsers;
                // remove the username from global usernames list
                if($socket->addedUser) {
                    unset($usernames[$socket->username]);
                    --$numUsers;
                    // echo globally that this client has left
                    $socket->broadcast->emit('user left', array(
                        'username' => $socket->username,
                        'numUsers' => $numUsers
                    ));
                }
            });
        });
    }
}
