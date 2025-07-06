<?php
/**
 * Ripcord
 *
 * @author      Lars Strojny <lars@strojny.net>
 * @license     http://opensource.org/licenses/lgpl-license.php LGPL
 * @link        http://code.google.com/p/ripcord/
 * @link        https://github.com/poef/ripcord
 */

define('RIPCORD_VERSION', '0.9.0');

class Ripcord_Exception extends Exception {}
class Ripcord_BadMethodCallException extends BadMethodCallException {}
class Ripcord_ConfigurationException extends Ripcord_Exception {}
class Ripcord_InvalidArgumentException extends InvalidArgumentException {}
class Ripcord_TransportException extends Ripcord_Exception {}
class Ripcord_RemoteException extends Ripcord_Exception
{
    public $faultCode;
    public $faultString;

    public function __construct($fault)
    {
        parent::__construct($fault->faultString, $fault->faultCode);
        $this->faultCode = $fault->faultCode;
        $this->faultString = $fault->faultString;
    }
}

class Ripcord_Transport_Stream
{
    protected $userAgent = 'Ripcord Client ' . RIPCORD_VERSION;
    protected $timeout = 15;
    protected $headers = [];
    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function addHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function post($url, $request, $contentType = 'text/xml')
    {
        $headers = '';
        foreach ($this->headers as $name => $value) {
            $headers .= "$name: $value\r\n";
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "User-Agent: {$this->userAgent}\r\n" .
                    "Content-Type: $contentType\r\n" .
                    $headers,
                'content' => $request,
                'timeout' => $this->timeout
            ]
        ]);

        $fp = @fopen($url, 'rb', false, $context);
        if (!$fp) {
            throw new Ripcord_TransportException("Unable to connect to $url");
        }

        $response = stream_get_contents($fp);
        fclose($fp);

        return $response;
    }
}

class Ripcord_Client
{
    protected $url;
    protected $transport;
    protected $isMulticall = false;

    public function __construct($url, Ripcord_Transport_Stream $transport = null)
    {
        $this->url = $url;
        $this->transport = $transport ?: new Ripcord_Transport_Stream();
    }

    public function __call($method, $args)
    {
        $request = xmlrpc_encode_request($method, $args);
        $response = $this->transport->post($this->url, $request);
        $decoded = xmlrpc_decode($response);

        if (is_array($decoded) && xmlrpc_is_fault($decoded)) {
            throw new Ripcord_RemoteException((object) $decoded);
        }

        return $decoded;
    }
}

class ripcord
{
    public static function client($url, Ripcord_Transport_Stream $transport = null)
    {
        return new Ripcord_Client($url, $transport);
    }
}
