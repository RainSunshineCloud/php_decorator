<?php

class TestDecorator
{
    public function echoStart($params)
    {
        echo "init".PHP_EOL;
        yield $params;
        $params->set("id",232);
        echo "finish".PHP_EOL;
        echo "23434".PHP_EOL;
        yield $params;
        echo 23434;
    }
}