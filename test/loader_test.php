<?php

include "../src/Loader.php";
include "../src/LoaderException.php";
include "../src/ParamsInterface.php";
include "./test.class.php";
include "./testDecorator.class.php";





class Params implements ParamsInterface
{
    protected $values = [];
    public function get($key,$default_value = null)
    {
        return $this->has($key) ? $this->values[$key] : $default_value;
    }
    public function set($key,$value)
    {
        $this->values[$key] = $value;
        return $this;
    }

    public function has($key)
    {
        return isset($this->values[$key]);
    }
}


try{
    $loader = new Loader();
    $params = new Params();
    $params->set("sdf","34543");
    $loader->exec('Test','echoTest',$params);
} catch(Exception $e) {
    var_dump($e->getMessage());
}
