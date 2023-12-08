<?php



namespace Solenoid\System;



class CallStack
{
    # Returns [array<assoc>]
    public static function fetch ()
    {
        // Returning the value
        return debug_backtrace();
    }

    # Returns [assoc]
    public static function fetch_origin ()
    {
        // (Getting the value)
        $list = self::fetch();
        $list = $list[ count( $list ) - 1 ];



        // Returning the value
        return $list;
    }
}



?>