<?php



namespace Solenoid\System;



use \Solenoid\System\Resource;



class Directory extends Resource
{
    # Returns [self] | Throws [Exception]
    public function __construct (string $path)
    {
        // (Getting the value)
        $resource = Resource::select( $path );

        if ( $resource->exists() && !$resource->is_dir() )
        {// (Resource is not a directory)
            // (Setting the value)
            $message = "Resource is not a directory";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }



        // (Calling the function)
        parent::__construct( $path );        
    }

    # Returns [Directory]
    public static function select (string $path)
    {
        // Returning the value
        return new Directory( $path );
    }



    # Returns [Directory|false]
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
        return Directory::select( $resource );
    }



    # Returns [array<string>]
    public static function traverse (string $path, array &$resources = [])
    {
        // (Getting the value)
        $entries = array_filter( scandir( $path ), function ($path) { return !in_array( $path, [ '.', '..' ] ); } );

        foreach ($entries as $entry)
        {// Processing each entry
            // (Getting the value)
            $resource = Resource::select( "$path/$entry" );



            // (Appending the value)
            $resources[] = $resource->get_path();



            if ( $resource->is_dir() )
            {// (Resource is a directory)
                // (Calling the function)
                self::traverse( $resource->get_path(), $resources );
            }
        }



        // Returning the value
        return $resources;
    }



    # Returns [array<string>]
    public function list (int $depth = 0, ?string $regex = null)
    {
        // Returning the value
        return
            array_values
            (
                array_filter
                (
                    self::traverse( $this->path ),
                    function ($resource) use ($regex, $depth)
                    {
                        // Returning the value
                        return
                            (
                                !$regex || ( $regex && preg_match( $regex, $resource ) === 1 )
                            )
                                &&
                            (
                                $depth === 0 || ( $depth > 0 && Resource::select( $resource )->get_depth() - $this->get_depth() === $depth )
                            )
                        ;
                    }
                )
            )
        ;
    }



    # Returns [self|false] | Throws [Exception]
    public function make (?int $umask = null)
    {
        /*

        // (Getting the value)
        $path_esa = escapeshellarg( $this->normalize()->get_path() );



        // (Executing the command)
        $result = shell_exec("mkdir -p $path_esa 2>&1");

        if ( $result !== null )
        {// (Unable to make the directory)
            // (Getting the value)
            $result = str_replace( "\n", ' >> ', $result );



            // (Setting the value)
            $message = "Unable to make the directory :: `$result`";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }

        */



        if ( $umask !== null )
        {// Value found
            // (Setting the umask)
            $current_umask = umask( $umask );
        }



        // (Getting the value)
        $folder_path = $this->normalize()->get_path();

        if ( !mkdir( $folder_path, 0777, true ) )
        {// (Unable to make the directory)
            // (Setting the value)
            $message = "Unable to make the directory '$folder_path'";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        if ( $umask !== null )
        {// Value found
            // (Setting the umask)
            umask( $current_umask );
        }



        // Returning the value
        return $this;
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

    # Returns [self|false] | Throws [Exception]
    public function empty ()
    {
        if ( substr_count( $this->path, '/' ) === 0 )
        {// Match failed
            // (Setting the value)
            $message = "Unable to remove nested resources :: Path is not valid";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // (Getting the value)
        $path_esa = escapeshellarg( $this->path ) . '/*';



        // (Executing the command)
        $result = shell_exec("rm -rf $path_esa 2>&1");

        if ( $result !== null )
        {// (Unable to remove nested resources)
            // (Getting the value)
            $result = str_replace( "\n", ' >> ', $result );



            // (Setting the value)
            $message = "Unable to remove nested resources :: `$result`";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $this;
    }



    # Returns [Directory|false] | Throws [Exception]
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
        return Directory::select( $resource->get_path() );
    }

    # Returns [Directory|false] | Throws [Exception]
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
        return Directory::select( $resource->get_path() );
    }



    # Returns [Directory]
    public function fetch_parent ()
    {
        // Returning the value
        return Directory::select( dirname( $this->path ) );
    }
}



?>