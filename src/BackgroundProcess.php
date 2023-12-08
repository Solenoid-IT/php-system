<?php



namespace Solenoid\System;



use \Solenoid\System\Process;



class BackgroundProcess
{
    private string $cwd;
    private string $cmd;
    
    private string $input;

    private string $pid;



    # Returns [self]
    public function __construct (string $cwd, string $cmd, string $input = '')
    {
        // (Getting the values)
        $this->cwd   = $cwd;
        $this->cmd   = $cmd;

        $this->input = $input;



        // (Setting the value)
        $this->pid = '';
    }

    # Returns [BackgroundProcess]
    public static function create (string $cwd, string $cmd, string $input = '')
    {
        // Returning the value
        return new BackgroundProcess( $cwd, $cmd, $input );
    }



    # Returns [self|false] | Throws [Exception]
    public function spawn ()
    {
        if ( !chdir( $this->cwd ) )
        {// (Unable to set the current directory)
            // (Setting the value)
            $message = "Unable to set the current directory";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // (Setting the value)
        $input = '';

        if ( $this->input )
        {// Value is not empty
            // (Creating a temp file)
            $input_file_path = tempnam( '/tmp', 'BGPRIN_' );

            if ( $input_file_path === false )
            {// (Unable to create a temp file)
                // (Setting the value)
                $message = "Unable to create a temp file";

                // Throwing an exception
                throw new \Exception($message);

                // Returning the value
                return false;
            }



            // (Opening the stream)
            $stdin = Stream::open( $input_file_path, 'w' );

            if ( $stdin === false )
            {// (Unable to open the stream)
                // (Setting the value)
                $message = "Unable to open the stream";

                // Throwing an exception
                throw new \Exception($message);

                // Returning the value
                return false;
            }



            // (Getting the value)
            $stdin_content = json_encode
            (
                [
                    'file_path' => $input_file_path,
                    'data'      => $this->input
                ]
            )
            ;

            if ( $stdin->write( $stdin_content ) === false )
            {// (Unable to write to the stream)
                // (Setting the value)
                $message = "Unable to write to the stream";

                // Throwing an exception
                throw new \Exception($message);

                // Returning the value
                return false;
            }



            // (Getting the value)
            $input_file_path_esa = escapeshellarg( $input_file_path );



            // (Getting the value)
            $input = " < $input_file_path_esa";
        }



        // (Executing the command)
        $this->pid = trim( shell_exec("exec $this->cmd{$input} > /dev/null 2>&1 & disown & echo $!") );



        // Returning the value
        return $this;
    }



    # Returns [string]
    public function get_pid ()
    {
        // Returning the value
        return $this->pid;
    }

    # Returns [bool]
    public function is_active ()
    {
        // Returning the value
        return explode( "\n", shell_exec("ps $this->pid") ) > 2;
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



        // (Getting the value)
        $input = json_decode( $input, true );



        if ( !unlink( $input['file_path'] ) )
        {// (Unable to remove the temp file)
            // (Setting the value)
            $message = "Unable to remove the temp";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return false;
        }



        // Returning the value
        return $input['data'];
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->get_pid();
    }
}



?>