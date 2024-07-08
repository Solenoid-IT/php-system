<?php



namespace Solenoid\System;



use \Solenoid\System\Stream;



class Process
{
    public int $pid;



    # Returns [self]
    public function __construct (string $pid)
    {
        // (Getting the value)
        $this->pid = $pid;        
    }



    # Returns [string|null]
    public static function run (string $cmd)
    {
        // Returning the value
        return shell_exec($cmd);
    }



    # Returns [Process|false]
    public static function start (string $cmd)
    {
        // (Executing the command)
        $output = shell_exec("nohup $cmd >/dev/null 2>&1 & echo $!");

        if ( $output === null || trim($output) === '' ) return false;



        // (Getting the value)
        $pid = (int) trim($output);



        // Returning the value
        return new Process($pid);
    }

    # Returns [self]
    public function wait ()
    {
        while ( true )
        {// Processing each clock
            if ( self::fetch_pid_info( $this->pid ) === false )
            {// (Process is not running)
                // Returning the value
                return $this;
            }



            // (Waiting for the time)
            sleep(1);
        }



        // Returning the value
        return $this;
    }



    # Returns [void]
    public static function kill (int $pid, int $signal = SIGTERM)
    {
        // (Executing the command)
        echo shell_exec("kill -$signal $pid");
    }



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
        // (Getting the value)
        $raw = shell_exec('ps aux');



        // (Getting the value)
        $lines = explode( "\n", $raw );



        // (Setting the values)
        $schema  = [];
        $records = [];



        // (Setting the value)
        $counter = 0;

        foreach ( $lines as $line )
        {// Processing each entry
            if ( trim($line) === '' ) continue;



            // (Incrementing the value)
            $counter += 1;



            // (Getting the value)
            $columns = preg_split( '/\s+/', $line, 11 );



            if ( $counter === 1 )
            {// (Row contains a schema)
                // (Getting the value)
                $schema = $columns;
            }
            else
            {// (Row contains a record)
                // (Setting the value)
                $record = [];

                foreach ( $columns as $k => $v )
                {// Processing each entry
                    // (Getting the value)
                    $record[ $schema[$k] ] = $v;
                }



                // (Appending the value)
                $records[] = $record;
            }
        }



        // Returning the value
        return $records;
    }

    # Returns [assoc|false]
    public static function fetch_pid_info (int $pid)
    {
        foreach ( self::list() as $record )
        {// Processing each entry
            if ( $record['PID'] !== (string) $pid ) continue;



            // Returning the value
            return $record;
        }



        // Returning the value
        return false;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return (string) $this->pid;
    }
}



?>