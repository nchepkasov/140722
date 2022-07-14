<?php 

namespace App\Helpers;

class StringUtils
{
    public static function getRandomString (int $length = 64, string $keyspace 
        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string 
    {
        $pieces = [];
        
        $max = mb_strlen ($keyspace, '8bit') - 1;
        
        for ($i = 0; $i < $length; ++$i) 
        {
            $pieces [] = $keyspace [random_int (0, $max)];
        }
        
        return implode ('', $pieces);
    }
}