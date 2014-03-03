<?php
namespace MeadSteve\Raygun4php;

class RequestMessage
{
    public $hostName;
    public $url;
    public $httpMethod;
    public $ipAddress;

    public $queryString;
    public $headers;
    public $data;
    public $form;
    public $rawData;

    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            $this->hostName = $_SERVER['HTTP_HOST'];
            $this->httpMethod = $_SERVER['REQUEST_METHOD'];
            $this->url = $_SERVER['REQUEST_URI'];
            $this->ipAddress = $_SERVER['REMOTE_ADDR'];

            parse_str($_SERVER['QUERY_STRING'], $this->queryString);
            if (empty($this->queryString)) {
                $this->queryString = null;
            }
        }

        $this->headers = $this->emu_getAllHeaders();
        $this->data = $_SERVER;

        $utf8_convert = function ($value) use (&$utf8_convert) {
            return is_array($value) ?
            array_map($utf8_convert, $value) :
            iconv('UTF-8', 'UTF-8//IGNORE', $value);
        };
        $this->form = array_map($utf8_convert, $_POST);

        if (php_sapi_name() !== 'cli') {
            $contentType = null;
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $contentType = $_SERVER['CONTENT_TYPE'];
            } elseif (isset($_SERVER['HTTP_CONTENT_TYPE'])) {
                $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
            }

            if ($_SERVER['REQUEST_METHOD'] != 'GET' &&
                $contentType != null &&
                $contentType != 'application/x-www-form-urlencoded' &&
                $contentType != 'multipart/form-data' &&
                $contentType != 'text/html'
            ) {
                $this->rawData = iconv('UTF-8', 'UTF-8//IGNORE', file_get_contents('php://input'));
            }
        }
    }

    private function emu_getAllHeaders()
    {
        if (!function_exists('getallheaders')) {
            $headers = '';
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))]
                        = $value;
                }
            }
            return $headers;
        } else {
            return getallheaders();
        }
    }
}
