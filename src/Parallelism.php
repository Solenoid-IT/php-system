<?php



namespace Solenoid\System;



class Parallelism
{
    private array $functions;



    # Returns [self]
    public function __construct (array $functions)
    {
        // (Getting the value)
        $this->functions = $functions;        
    }

    # Returns [Parallelism]
    public static function create (array $functions)
    {
        // Returning the value
        return new Parallelism( $functions );
    }



    # Returns [void] | Throws [Exception]
    public function run ()
    {
        foreach ($this->functions as $function)
        {// Processing each entry
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
        }

        // (Waiting for the child process termination)
        while ( pcntl_waitpid( 0, $status ) !== -1 );
    }
}



?>