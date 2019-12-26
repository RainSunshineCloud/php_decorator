<?php
interface ParamsInterface
{
    public function set($key,$value);
    public function get($key,$default_value);
    public function has($key);
}
