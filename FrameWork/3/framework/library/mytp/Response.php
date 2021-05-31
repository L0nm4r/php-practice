<?php
namespace mytp;

class Response
{
    protected $data;
    protected $code = 200;
    protected $header = [];
    protected $contentType = 'text/html';
    protected $charset = 'utf-8';

    public function __construct($data = '', $code = 200, array $header = [])
    {
        $this->data($data);
        $this->contentType($this->contentType, $this->charset);
        $this->code = $code;
        $this->header = array_merge($this->header, $header);
    }

    public function contentType($contentType, $charset = 'utf-8')
    {
        $this->header['Content-Type'] = $contentType . '; charset=' . $charset;
    }

    public function data($data)
    {
        $this->data = $data;
    }

    public static function create($data = '', $type = '', $code = 200, array $header = [])
    {
        switch (strtolower($type)) {
            case 'json':
                $data = json_encode($data);
                $header['Content-Type'] = 'application/json; charset=utf-8';
                break;
        }
        return new static($data, $code, $header);
    }

    public function send()
    {
        http_response_code($this->code);
        foreach ($this->header as $name => $value) {
            header($name . (is_null($value) ? '' : ':' . $value));
        }
        echo $this->data;
    }
}
