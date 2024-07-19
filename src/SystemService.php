<?php



namespace Solenoid\System;



class SystemService
{
    protected int  $refresh_interval;

    protected bool $running;
    protected      $handler;



    public int     $startup_ts;



    # Returns [self]
    private function signal_handler ($signal)
    {
        switch ($signal)
        {
            case SIGCHLD:
                // (Waiting for the child process)
                $pid = pcntl_waitpid( -1, $status, WNOHANG );

                while ( $pid > 0 )
                {// Processing each clock
                    // (Getting the value)
                    $exit_code = pcntl_wexitstatus($status);

                    // (Getting the value)
                    $pid = pcntl_waitpid( -1, $status, WNOHANG );
                }
            break;

            default:
                // (Calling the function)
                ( $this->handler )($signal);



                // (Setting the value)
                $this->running = false;
        }



        // Returning the value
        return $this;
    }



    # Returns [self]
    public function __construct (int $refresh_interval = 1)
    {
        // (Getting the value)
        $this->refresh_interval = $refresh_interval;
    }



    # Returns [self]
    public function handle_signal (callable $handler)
    {
        // (Getting the value)
        $this->handler = $handler;



        // Returning the value
        return $this;
    }

    # Returns [self]
    public function run (callable $startup, callable $tick)
    {
        // (Setting the time limit)
        set_time_limit(0);



        // (Registering the handlers)
        pcntl_signal( SIGTERM, [ $this, 'signal_handler' ] );
        pcntl_signal( SIGHUP, [ $this, 'signal_handler' ] );
        pcntl_signal( SIGINT, [ $this, 'signal_handler' ] );
        pcntl_signal( SIGCHLD, [ $this, 'signal_handler' ] );



        // (Setting the value)
        $this->running = true;



        // (Getting the value)
        $this->startup_ts = time();

        // (Calling the function)
        $startup();



        // (Declaring the variable)
        declare ( ticks = 1 )
        {
            while ( $this->running )
            {
                // (Calling the function)
                $tick();



                // (Waiting for the time)
                sleep( $this->refresh_interval );
            }
        }



        // Returning the value
        return $this;
    }
}



?>