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



    # Returns [assoc]
    public function read ()
    {
        // Returning the value
        return JDB::load( $this->file_path )->data;
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



    # Returns [bool]
    public function exists ()
    {
        // Returning the value
        return file_exists( $this->file_path );
    }

    # Returns [bool]
    public function remove ()
    {
        // Returning the value
        return unlink( $this->file_path );
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return json_encode( $this->data );
    }
}



?>