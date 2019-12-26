<?php
class Test 
{
    /**
     * @Decorator TestDecorator->echoStart
     * @return [type] [description]
     */
    public function echoTest($params)
    {
        
        echo $params->get("id",45).PHP_EOL;
    }
}