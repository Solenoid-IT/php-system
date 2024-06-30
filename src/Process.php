<?php



namespace Solenoid\System;



use \Solenoid\System\Stream;
use \Solenoid\CSV\Parser as CSVParser;



class Process
{
    # Returns [string|false] | Throws [Exception]
    public static function read ()
    {
        // (Getting the value)
        $content = Stream::handle( STDIN )->read();

        if ( $content === false )
        {// (Unable to read the content from the stream)
            // (Setting the value)
            $message = "Unable to read the content from the stream";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $content;
    }

    # Returns [int|false] | Throws [Exception]
    public static function write (string $data)
    {
        // (Getting the value)
        $wb = Stream::handle( STDOUT )->write( $data );

        if ( $wb === false )
        {// (Unable to write the content to the stream)
            // (Setting the value)
            $message = "Unable to write the content to the stream";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $wb;
    }



    # Returns [void] | Throws [Exception]
    public static function execute (callable $function, bool $async = false)
    {
        // (Forking the process)
        $pid = pcntl_fork();

        if ( $pid === -1 )
        {// (Unable to fork the process)
            // (Setting the value)
            $message = "Unable to fork the process";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }

        if ( $pid === 0 )
        {// (Process is the child one)
            // (Calling the function)
            $function();

            // (Closing the process)
            exit;
        }



        if ( !$async )
        {// Match OK
            // (Waiting for the child process termination)
            while ( pcntl_waitpid( 0, $status ) !== -1 );
        }
    }



    # Returns [void]
    public static function register_shutdown_callback (callable $function)
    {
        #declare(ticks = 1); // enable signal handling



        // (Setting the handlers)
        pcntl_signal( SIGINT, $function );  
        pcntl_signal( SIGTERM, $function ); 
    }



    # Returns [array<assoc>]
    public static function list ()
    {
        // Returning the value
        return CSVParser::parse( shell_exec('ps aux'), "\n", ' ' );
    }
}



?>