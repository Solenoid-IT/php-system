<?php



namespace Solenoid\System;



use \Solenoid\System\Resource;
use \Solenoid\System\Directory;
use \Solenoid\System\Stream;



class File extends Resource
{
    # Returns [self] | Throws [Exception]
    public function __construct (string $path)
    {
        // (Getting the value)
        $resource = Resource::select( $path );

        if ( $resource->exists() && !$resource->is_file() )
        {// (Resource is not a file)
            // (Setting the value)
            $message = "Resource is not a file";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }



        // (Calling the function)
        parent::__construct( $path );
    }

    # Returns [File]
    public static function select (string $path)
    {
        // Returning the value
        return new File( $path );
    }



    # Returns [File|false]
    public function resolve ()
    {
        // (Getting the value)
        $resource = parent::resolve();

        if ( $resource === false )
        {// (Unable to resolve the path)
            // Returning the value
            return false;
        }



        // Returning the value
        return File::select( $resource );
    }



    # Returns [File|false] | Throws [Exception]
    public function move (string $dst_path)
    {
        // (Moving the resource)
        $resource = parent::move( $dst_path );

        if ( $resource === false )
        {// (Unable to move the resource)
            // (Setting the value)
            $message = "Unable to move the resource";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return File::select( $resource->get_path() );
    }

    # Returns [File|false] | Throws [Exception]
    public function copy (string $dst_path)
    {
        // (Copying the resource)
        $resource = parent::copy( $dst_path );

        if ( $resource === false )
        {// (Unable to copy the resource)
            // (Setting the value)
            $message = "Unable to copy the resource";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return File::select( $resource->get_path() );
    }



    # Returns [self|false] | Throws [Exception]
    public function remove ()
    {
        // (Removing the resource)
        $resource = parent::remove();

        if ( $resource === false )
        {// (Unable to remove the resource)
            // (Setting the value)
            $message = "Unable to remove the resource";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [int|false]
    public function get_size ()
    {
        // Returning the value
        return filesize( $this->path );
    }



    # Returns [string|false]
    public function get_metadata (string $key)
    {
        switch ($key)
        {
            case 'CREATION_TIME':
                // Returning the value
                return filectime( $this->path );
            break;

            case 'LAST_MODIFIED_TIME':
                // Returning the value
                return filemtime( $this->path );
            break;

            case 'LAST_ACCESS_TIME':
                // Returning the value
                return fileatime( $this->path );
            break;

            default:
                // Returning the value
                return false;
        }
    }



    # Returns [string|false]
    public function read ()
    {
        // Returning the value
        return file_get_contents( $this->path );
    }

    # Returns [File|false] | Throws [Exception]
    public function write (string $content = '', string $mode = 'replace')
    {
        // (Getting the value)
        $directory = $this->fetch_parent();

        if ( !$directory->exists() )
        {// (Directory not found)
            if ( !$directory->make() )
            {// (Unable to make the directory)
                // (Setting the value)
                $message = "Unable to make the directory";

                // Throwing an exception
                throw new \Exception($message);

                // Returning the value
                return false;
            }
        }



        if ( file_put_contents( $this->path, $content, $mode === 'replace' ? 0 : FILE_APPEND) === false )
        {// (Unable to write content to the file)
            // (Setting the value)
            $message = "Unable to write content to the file '$this->path'";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [void]
    public function walk (callable $handle_line, string $eol = PHP_EOL, ?string $regex = null)
    {
        // (Setting the value)
        $index = 0;



        // (Opening the stream)
        $stream = Stream::open( $this->path, 'r' );

        while ( !$stream->is_ended() )
        {// Processing each entry
            // (Incrementing the value)
            $index += 1;

            // (Getting the value)
            $line = $stream->read_line( 0, $eol );



            if ( $regex && preg_match( $regex, $line, $matches, PREG_OFFSET_CAPTURE ) === 0 )
            {// Match failed
                // Continuing the iteration
                continue;
            }



            if ( $handle_line( $line, $index, $matches ) === false )
            {// Value is true
                // Breaking the iteration
                break;
            }
        }

        // (Closing the stream)
        $stream->close();
    }



    # Returns [Directory]
    public function fetch_parent ()
    {
        // Returning the value
        return Directory::select( dirname( $this->path ) );
    }
}



?>