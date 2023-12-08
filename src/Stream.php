<?php



namespace Solenoid\System;



class Stream
{
    public $resource;



    # Returns [self]
    public function __construct ($resource)
    {
        // (Getting the value)
        $this->resource = $resource;
    }

    # Returns [Stream]
    public static function handle ($resource)
    {
        // Returning the value
        return new Stream( $resource );
    }



    # Returns [Stream|false] | Throws [Exception]
    public static function open (string $file_path, string $mode = 'r')
    {
        // (Opening the stream)
        $resource = fopen( $file_path, $mode );

        if ( $resource === false )
        {// (Unable to open the stream)
            // (Setting the value)
            $message = "Unable to open the stream";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return Stream::handle( $resource );
    }

    # Returns [bool] | Throws [Exception]
    public function close ()
    {
        if ( !fclose( $this->resource ) )
        {// Unable to close the stream
            // (Setting the value)
            $message = "Unable to close the stream";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [int|false] | Throws [Exception]
    public static function copy (Stream $input, Stream $output, ?int $length = null, int $offset = 0)
    {
        // (Copying to the stream)
        $wb = stream_copy_to_stream( $input->resource, $output->resource, $length, $offset );

        if ( $wb === false )
        {// (Unable to copy the content to the output stream)
            // (Setting the value)
            $message = "Unable to copy the content to the output stream";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $wb;
    }



    # Returns [bool] | Throws [Exception]
    public function set_lock (bool $value)
    {
        if ( !stream_set_blocking( $this->resource, $value ) )
        {// (Unable to set the stream blocking state)
            // (Setting the value)
            $message = "Unable to set the stream blocking state";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }

    # Returns [assoc] | Throws [Exception]
    public function get_metadata ()
    {
        // Returning the value
        return stream_get_meta_data( $this->resource );
    }



    # Returns [bool]
    public function is_ended ()
    {
        // Returning the value
        return feof( $this->resource );
    }



    # Returns [string|false] | Throws [Exception]
    public function get_content (?int $length = null, int $offset = -1)
    {
        // (Getting the value)
        $content = stream_get_contents( $this->resource, $length, $offset );

        if ( $content === false )
        {// (Unable to read the content)
            // (Setting the value)
            $message = "Unable to read the content";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $content;
    }



    # Returns [string|false] | Throws [Exception]
    public function read (?int $length = null)
    {
        if ( $length === null )
        {// Value not found
            // (Getting the value)
            $content = stream_get_contents( $this->resource );
        }
        else
        {// Value found
            // (Getting the value)
            $content = fread( $this->resource, $length );
        }



        if ( $content === false )
        {// (Unable to read the content)
            // (Setting the value)
            $message = "Unable to read the content";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $content;
    }

    # Returns [int|false] | Throws [Exception]
    public function write (string $data, ?int $length = null)
    {
        // (Writing to the stream)
        $wb = fwrite( $this->resource, $data, $length );

        if ( $wb === false )
        {// (Unable to write the content)
            // (Setting the value)
            $message = "Unable to write the content";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $wb;
    }



    # Returns [string|false]
    public function read_line (int $length = 0, string $separator = '')
    {
        // Returning the value
        return stream_get_line( $this->resource, $length, $separator );
    }
}



?>