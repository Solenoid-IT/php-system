<?php



namespace Solenoid\System;



class Byte
{
    public int $value;

    const FACTORS =
    [
        ''  => 0,

        'K' => 1,
        'M' => 2,
        'G' => 3,

        'T' => 4,
        'P' => 5,
        'E' => 6
    ]
    ;



    # Returns [self]
    public function __construct (int $value)
    {
        // (Getting the value)
        $this->value = $value;
    }

    # Returns [Byte]
    public static function create (int $value)
    {
        // Returning the value
        return new Byte( $value );
    }



    # Returns [int|false] | Throws [Exception]
    public static function convert (int $value, string $factor)
    {
        if ( !isset( self::FACTORS[ $factor ] ) )
        {// (Factor not found)
            // (Setting the value)
            $message = "Cannot convert the bytes :: Factor '$factor' is not recognized";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $value * pow( 1024, self::FACTORS[ $factor ] );
    }

    # Returns [int|false]
    public static function read (string $value)
    {
        if ( preg_match( '/^([\d]+)\s*([\w]*)$/', $value, $matches ) === 0 )
        {// (Regex does not match the text)
            // Returning the value
            return false;
        }



        // Returning the value
        return self::convert( (int) $matches[1], $matches[2] ?? '' );
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->value;
    }
}



?>