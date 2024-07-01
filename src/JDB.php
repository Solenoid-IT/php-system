<?php



namespace Solenoid\System;



use \Solenoid\System\File;



class JDB
{
    public string $file_path;
    public array  $data;



    # Returns [self]
    public function __construct (string $file_path)
    {
        // (Getting the value)
        $this->file_path = $file_path;

        // (Setting the value)
        $this->data = [];
    }



    # Returns [DB|false] | Throws [Exception]
    public static function load (string $file_path)
    {
        // (Getting the value)
        $db = new self($file_path);



        // (Getting the value)
        $file_content = file_get_contents($file_path);

        if ( $file_content === false )
        {// (Unable to read the file)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $db->data = json_decode( $file_content, true );



        // Returning the value
        return $db;
    }



    # Returns [self|false]
    public function save (bool $compress = false)
    {
        if ( File::select( $this->file_path )->write( json_encode( $this->data, $compress ? 0 : JSON_PRETTY_PRINT ) ) === false )
        {// (Unable to write to the file)
            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }

    # Returns [self|false]
    public function init ()
    {
        // (Setting the value)
        $this->data = [];

        

        // Returning the value
        return $this->save();
    }
}



?>