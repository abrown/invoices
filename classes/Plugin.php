<?php
class Plugin{
    public static function import($name){
        $file = Configuration::get('base_dir').DS.'plugins'.DS.$name.'.php';
        if( !is_file($file) ) throw new Exception("Plugin '$name' not found", 500);
        else require $file;
    }
}
