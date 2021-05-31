<?php
namespace mytp;

class Config
{
    protected $config = [];

    public function get($name, $default = null)
    {
        $config = $this->config;
        foreach (explode('.', $name) as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }
        return $config;
    }

    public function set($name, $value = null)
    {
        if (is_array($name)) {
            $this->config = array_replace_recursive($this->config, $name);
        } elseif (is_array($value)) {
            $this->config[$name] = array_replace_recursive($this->config[$name], $value);
        } else {
            $this->config[$name] = $value;
        }
    }
}
