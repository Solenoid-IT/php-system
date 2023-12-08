<?php



namespace Solenoid\System;



class EOL
{
    const VALUES =
    [
        "\r\n" => 'CRLF',
        "\n\r" => 'LFCR',
        "\n"   => 'LF',
        "\r"   => 'CR',
        "\025" => 'NL',
        "\036" => 'RS'
    ]
    ;



    public string $type;
    public string $value;



    # Returns [self]
    public function __construct (string $type, string $value)
    {
        // (Getting the values)
        $this->type  = $type;
        $this->value = $value;
    }

    # Returns [EOL]
    public static function create (string $type, string $value)
    {
        // Returning the value
        return new EOL( $type, $value );
    }



    # Returns [EOL|false]
    public static function detect (string $data)
    {
        foreach (self::VALUES as $value => $type)
        {// Processing each entry
            if ( substr_count( $data, $value ) )
            {// Value found
                // Returning the value
                return
                    EOL::create( $type, $value );
                ;
            }
        }



        // Returning the value
        return false;
    }



    # Returns [assoc]
    public function to_array ()
    {
        // Returning the value
        return get_object_vars( $this );
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->value;
    }
}



?>