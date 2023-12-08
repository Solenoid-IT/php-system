<?php



namespace Solenoid\System;



use \Solenoid\System\Process;



class ChildProcess
{
    private string $cwd;
    private string $cmd;

    private        $resource;
    private        $pipes;



    public Stream  $stdin;
    public Stream  $stdout;

    public Stream  $stderr;



    # Returns [self]
    public function __construct (string $cwd, string $cmd)
    {
        // (Getting the values)
        $this->cwd = $cwd;
        $this->cmd = $cmd;        
    }

    # Returns [ChildProcess]
    public static function create (string $cwd, string $cmd)
    {
        // Returning the value
        return new ChildProcess( $cwd, $cmd );
    }



    # Returns [self|false] | Throws [Exception]
    public function spawn (?string $error_file_path = null)
    {
        // (Setting the value)
        $descriptor =
        [
            0 => [ 'pipe', 'r' ],# 'stdin'
            1 => [ 'pipe', 'w' ],# 'stdout'
        ]
        ;

        if ( $error_file_path )
        {// Value found
            // (Appending the value)
            $descriptor[] = [ 'file', $error_file_path, 'a' ];# 'stderr'
        }



        // (Opening the process)
        $this->resource = proc_open
        (
            $this->cmd,
            $descriptor,
            $this->pipes,
            $this->cwd
        )
        ;

        if ( $this->resource === false )
        {// (Unable to open the process)
            // (Setting the value)
            $message = "Unable to open the process";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // (Getting the values)
        $this->stdin  = Stream::handle( $this->pipes[0] );
        $this->stdout = Stream::handle( $this->pipes[1] );

        if ( $error_file_path )
        {// Value found
            // (Getting the value)
            $this->stderr = Stream::handle( $this->pipes[2] );
        }



        // Returning the value
        return $this;
    }

    # Returns [int]
    public function wait ()
    {
        // Returning the value
        return proc_close( $this->resource );
    }



    # Returns [string|false] | Throws [Exception]
    public static function read ()
    {
        // (Getting the value)
        $input = Process::read();

        if ( $input === false )
        {// (Unable to read the process)
            // (Setting the value)
            $message = "Unable to read the process STDIN";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $input;
    }

    # Returns [int|false] | Throws [Exception]
    public static function write (string $data)
    {
        // (Getting the value)
        $wb = Process::write( $data );

        if ( $wb === false )
        {// (Unable to write to the process STDOUT)
            // (Setting the value)
            $message = "Unable to write to the process STDOUT";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $wb;
    }
}



?>