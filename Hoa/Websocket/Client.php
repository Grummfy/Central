<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Websocket\Connection
 */
-> import('Websocket.Connection')

/**
 * \Hoa\Http\Response
 */
-> import('Http.Response.~');

}

namespace Hoa\Websocket {

/**
 * Class \Hoa\Websocket\Client.
 *
 * A cross-protocol Websocket client.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Client extends Connection {

    /**
     * Endpoint.
     *
     * @var \Hoa\Websocket\Client string
     */
    protected $_endPoint = null;

    /**
     * Host name.
     *
     * @var \Hoa\Websocket\Client string
     */
    protected $_host     = null;

    /**
     * Response (mainly parser).
     *
     * @var \Hoa\Http\Response object
     */
    protected $_response = null;



    /**
     * Create a Websocket client.
     *
     * @access  public
     * @param   \Hoa\Socket\Client  $client      Client.
     * @param   string              $endPoint    End-point.
     * @param   \Hoa\Http\Response  $request     Response parser.
     * @return  void
     * @throw   \Hoa\Socket\Exception
     */
    public function __construct ( \Hoa\Socket\Client $client, $endPoint = '/',
                                  \Hoa\Http\Response $response = null ) {

        parent::__construct($client);
        $this->setEndPoint($endPoint);

        if(null === $response)
            $response = new \Hoa\Http\Response(false);

        $this->setResponse($response);

        return;
    }

    /**
     * Connect, i.e. open the connection and do handshake.
     *
     * @access  public
     * @return  void
     */
    public function connect ( ) {

        return $this->doHandshake();
    }

    /**
     * Override the parent run() method to open the connection.
     *
     * @access  public
     * @return  void
     */
    public function run ( ) {

        $this->connect();

        return parent::run();
    }

    /**
     * Receive data. Fire listeners.
     *
     * @access  public
     * @return  void
     */
    public function receive ( ) {

        $connection = $this->getConnection();
        $node       = $connection->getCurrentNode();

        do {

            $this->_run($node);

        } while(   false === $connection->isDisconnected()
                &&  true !== $node->isMessageComplete());
    }

    /**
     * Try the handshake by trying different protocol implementation.
     *
     * @access  protected
     * @return  void
     * @throw   \Hoa\Websocket\Exception
     * @throw   \Hoa\Websocket\Exception\BadProtocol
     */
    protected function doHandshake ( ) {

        static $_tail = array('A', 'Q', 'g', 'w');

        $connection = $this->getConnection();
        $connection->connect();
        $key        = substr(base64_encode(\Hoa\Core::uuid()), 0, 21) .
                      $_tail[mt_rand(0, 3)] . '==';
        $expected   = base64_encode(sha1($key . Protocol\Rfc6455::GUID, true));

        if(null === $host = $this->getHost())
            throw new Exception(
                'Host name is null. Please, use the %s::setHost() method.',
                0, __CLASS__);

        $connection->writeAll($request =
            'GET ' . $this->getEndPoint() . ' HTTP/1.1' . CRLF .
            'Host: ' . $host . CRLF .
            'User-Agent: Hoa' . CRLF .
            'Upgrade: WebSocket' . CRLF .
            'Connection: Upgrade' . CRLF .
            'Pragma: no-cache' . CRLF .
            'Cache-Control: no-cache' . CRLF .
            'Sec-WebSocket-Key: ' . $key . CRLF .
            'Sec-WebSocket-Version: 13' . CRLF . CRLF
        );

        $buffer   = $connection->read(2048);
        $response = $this->getResponse();
        $response->parse($buffer);

        if(   $response::STATUS_SWITCHING_PROTOCOLS !== $response['status']
           || 'websocket' !== strtolower($response['upgrade'])
           ||   'upgrade' !== strtolower($response['connection'])
           ||   $expected !== $response['sec-websocket-accept'])
            throw new Exception\BadProtocol(
                'Handshake has failed, the server did not return a valid ' .
                'response.' . "\n\n" .
                'Client:' . "\n" . '    %s' . "\n" .
                'Server:' . "\n" . '    %s',
                0, array(
                    str_replace("\n", "\n" . '    ', $request),
                    str_replace("\n", "\n" . '    ', $buffer)
                ));

        $currentNode = $connection->getCurrentNode();
        $currentNode->setHandshake(SUCCEED);
        $currentNode->setProtocolImplementation(new Protocol\Rfc6455($connection));

        $this->_on->fire(
            'open',
            new \Hoa\Core\Event\Bucket()
        );

        return;
    }

    /**
     * Set end-point.
     *
     * @access  protected
     * @param   string  $endPoint    End-point.
     * @return  string
     */
    protected function setEndPoint ( $endPoint ) {

        $old             = $this->_endPoint;
        $this->_endPoint = $endPoint;

        return $old;
    }

    /**
     * Get end-point.
     *
     * @access  public
     * @return  string
     */
    public function getEndPoint ( ) {

        return $this->_endPoint;
    }

    /**
     * Set response (mainly parser).
     *
     * @access  public
     * @param   \Hoa\Http\Response  $response    Response.
     * @return  \Hoa\Http\Response
     */
    public function setResponse ( \Hoa\Http\Response $response ) {

        $old             = $this->_response;
        $this->_response = $response;

        return $old;
    }

    /**
     * Get response.
     *
     * @access  public
     * @return  \Hoa\Http\Response
     */
    public function getResponse ( ) {

        return $this->_response;
    }

    /**
     * Set host.
     *
     * @access  public
     * @param   string  $host    Host.
     * @return  string
     */
    public function setHost ( $host ) {

        $old         = $this->_host;
        $this->_host = $host;

        return $old;
    }

    /**
     * Get host.
     *
     * @access  public
     * @return  string
     */
    public function getHost ( ) {

        return null !== $this->_host
                   ? $this->_host
                   : (isset($_SERVER['HTTP_HOST'])
                         ? $_SERVER['HTTP_HOST']
                         : null);
    }
}

}
