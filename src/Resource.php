<?php



namespace Solenoid\System;



class Resource
{
    protected string $path;



    # Returns [self]
    public function __construct (string $path)
    {
        // (Getting the value)
        $this->path = $path;
    }

    # Returns [Resource]
    public static function select (string $path)
    {
        // Returning the value
        return new Resource( $path );
    }



    # Returns [bool]
    public function is_file ()
    {
        // Returning the value
        return file_exists( $this->path ) && !is_dir( $this->path );
    }

    # Returns [bool]
    public function is_dir ()
    {
        // Returning the value
        return is_dir( $this->path );
    }



    # Returns [bool]
    public function is_link ()
    {
        // Returning the value
        return is_link( $this->path );
    }



    # Returns [bool]
    public function exists ()
    {
        // Returning the value
        return $this->is_file( $this->path ) || $this->is_dir( $this->path );
    }



    # Returns [Resource|false]
    public function resolve ()
    {
        // (Getting the value)
        $path = $this->is_link( $this->path ) ? readlink( $this->path ) : realpath( $this->path );

        if ( $path === false )
        {// (Unable to resolve the path)
            // Returning the value
            return false;
        }



        // Returning the value
        return Resource::select( $path );
    }

    # Returns [Resource]
    public function diff (Resource $resource)
    {
        // Returning the value
        return Resource::select( implode( '/', array_diff( explode( '/', $this ), explode( '/', $resource ) ) ) );
    }



    # Returns [string]
    public function get_path ()
    {
        // Returning the value
        return $this->path;
    }

    # Returns [string|false]
    public function get_type ()
    {
        // Returning the value
        return $this->is_file() ? mime_content_type( $this->path ) : 'dir';
    }



    # Returns [string|false] | Throws [Exception]
    public function get_perms ()
    {
        // (Getting the value)
        $perms = fileperms( $this->path );

        if ( $perms === false )
        {// (Unable to get the permissions)
            // (Setting the value)
            $message = "Unable to get the permissions";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return substr( sprintf( '%o', $perms ), -4 );
    }

    # Returns [Resource|false] | Throws [Exception]
    public function set_perms (int $value, bool $recursive = false)
    {
        // (Getting the values)
        $path_esa  = escapeshellarg( $this->path );
        $recursive = $recursive ? '-R' : '';



        // (Executing the command)
        $result = shell_exec("chmod $recursive $value $path_esa 2>&1");

        if ( $result !== null )
        {// (Unable to set the permissions)
            // (Getting the value)
            $result = str_replace( "\n", ' >> ', $result );



            // (Setting the value)
            $message = "Unable to set the permissions :: `$result`";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [int]
    public function get_depth ()
    {
        // Returning the value
        return substr_count( $this->path, '/' );
    }



    # Returns [Resource]
    public function normalize ()
    {
        // (Getting the values)
        $parts     = explode( '/', $this->path );
        $num_parts = count( $parts );

        if ( $num_parts > 1 && $parts[ $num_parts - 1 ] === '' )
        {// (Path ends with '/')
            // (Popping the array)
            array_pop( $parts );
        }



        for ($i = 0; $i < count( $parts ); $i++)
        {// Iterating each index
            if ( $parts[ $i ] === '..' )
            {// Match OK
                // (Popping the array)
                unset( $parts[ $i ] );
                unset( $parts[ $i - 1 ] );

                // (Getting the value)
                $parts = array_values( $parts );

                // (Setting the value)
                $i = 0;
            }
        }



        // Returning the value
        return Resource::select( implode( '/', $parts ) );
    }



    # Returns [Resource|false] | Throws [Exception]
    public function move (string $dst_path)
    {
        // (Getting the values)
        $src_esa = escapeshellarg( $this->path );
        $dst_esa = escapeshellarg( $dst_path );



        // (Executing the command)
        $result = shell_exec("mv $src_esa $dst_esa 2>&1");

        if ( $result !== null )
        {// (Unable to move the resource content to the destination path)
            // (Getting the value)
            $result = str_replace( "\n", ' >> ', $result );



            // (Setting the value)
            $message = "Unable to move the resource content to the destination path :: `$result`";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return Resource::select( $dst_path );
    }

    # Returns [Resource|false] | Throws [Exception]
    public function copy (string $dst_path)
    {
        // (Getting the values)
        $src_esa = escapeshellarg( $this->path );
        $dst_esa = escapeshellarg( $dst_path );



        // (Executing the command)
        $result = shell_exec("cp -a $src_esa $dst_esa 2>&1");

        if ( $result !== null )
        {// (Unable to copy the resource content to the destination path)
            // (Getting the value)
            $result = str_replace( "\n", ' >> ', $result );



            // (Setting the value)
            $message = "Unable to copy the resource content to the destination path :: `$result`";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return Resource::select( $dst_path );
    }



    # Returns [self|false] | Throws [Exception]
    public function remove ()
    {
        // (Getting the values)
        $path_esa = escapeshellarg( $this->path );



        // (Executing the command)
        $result = shell_exec("rm -rf $path_esa 2>&1");

        if ( $result !== null )
        {// (Unable to remove the resource)
            // (Getting the value)
            $result = str_replace( "\n", ' >> ', $result );



            // (Setting the value)
            $message = "Unable to remove the resource :: `$result`";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [string]
    public function __toString ()
    {
        // (Getting the value)
        $resource = $this->resolve();



        // Returning the value
        return $resource ? $resource->get_path() : $this->get_path();
    }
}



?>