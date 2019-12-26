<?php

class Loader
{
    /**
     * 执行参数
     * @param  [string]          $class_name    [类名]
     * @param  [string]          $function_name [函数名]
     * @param  ParamsInterface   $params        [参数]
     */
    public function exec(string $class_name,string $function_name,ParamsInterface $params) 
    {
        $this->checkMethodExist($class_name,$function_name);
        $call_decorator_function = $this->getDecoratorNameArray($class_name,$function_name);
        $decoretor_obj = $this->getDecoratorFunction($call_decorator_function,$params);
       
        $reflect = new ReflectionMethod($class_name,$function_name);
        if (!$reflect->isStatic()) {
            $self = new $class_name();
        } else {
            $self = $class_name;
        }

        $is_boot = true;
        if ($decoretor_obj instanceof Iterator) {
            foreach ($decoretor_obj as $params) {
                if (!$params instanceof ParamsInterface) {
                    throw new LoaderException("装饰器返回值必须是ParamInterface的返回参数",100);
                }

                if ($is_boot) {
                    call_user_func([$self,$function_name],$params);
                } 
                $is_boot = false;
                
            }
        } else {
            call_user_func([$self,$function_name],$params);
        } 
    }

    /**
     * 获取装饰器函数
     * @param  array           $call_decorator_function [装饰器名]
     * @param  ParamsInterface $params                  [参数]
     * @return [type]                                   [description]
     */
    protected function getDecoratorFunction(array $call_decorator_function,ParamsInterface $params)
    {
        $len =count($call_decorator_function);
        if ($len == 0) {
           $obj = $this->defaultDecorator($params);
        } else if ($len == 1) {
           $obj = call_user_func($call_decorator_function[0],$params);
        } else if ($len == 2) {
            $obj = call_user_func($call_decorator_function,$params);
        } else {
            throw new LoaderException("装饰器写法错误",200);
        }

        return $obj;
    }

    /**
     * 默认装饰器
     * @param  ParamsInterface $params [参数]
     * @return [type]                  [description]
     */
    public function defaultDecorator(ParamsInterface $params)
    {
       yield $params;
    }

    /**
     * 获取装饰器类名方法名数组
     * @param  string $class_name    [description]
     * @param  string $function_name [description]
     * @return [type]                [description]
     */
    protected function getDecoratorNameArray(string $class_name,string $function_name)
    {
        $reflction_method = new ReflectionMethod($class_name,$function_name);
        $text = $reflction_method->getDocComment();
        $is_static = $reflction_method->isStatic();
        return $this->parseDocComment($text,$is_static);
    }

    /**
     * 判断方法是否存在
     * @param  string $class_name    [类名]
     * @param  string $function_name [方法名]
     * @return [type]                [description]
     */
    protected function checkMethodExist(string $class_name,string $function_name) 
    {
        if (!class_exists($class_name)) {
            $message = sprintf("[%s]类名不存在",$class_name);
            throw new LoaderException($message,300);
        }

        if (!method_exists($class_name, $function_name)) {
            $message = sprintf("[%s]->[%s]方法名不存在",$class_name,$function_name);
            throw new LoaderException($message,400);
        }

    }

    /**
     * 转化注释
     * @param  string       $text      [文字注释]
     * @param  bool|boolean $is_static [是否是静态方法]
     * @return [type]                  [description]
     */
    protected function parseDocComment(string $text,bool $is_static = true) 
    {
        $matchs = $params = [];
        preg_match('/@Decorator\s*(\w{1,30})(->(\w{1,30})){0,1}/',$text,$matchs);
        
        if (isset($matchs[3])) {
            array_push($params,$matchs[3]);
        }

        if (isset($matchs[1])) {
            if (!$is_static) {
                $obj = new $matchs[1]();
            } else {
                $obj = $matchs[1];
            }
            
            array_unshift($params,$obj);
        }
        return $params;
    }
}
