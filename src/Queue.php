<?php



namespace Solenoid\System;



class Queue
{
    private $q;



    # Returns [self]
    public function __construct (array $elements = [])
    {
        // (Creating an SplQueue)
        $this->q = new \SplQueue();



        foreach ($elements as $element)
        {// Processing each entry
            // (Enqueuing the element)
            $this->enqueue( $element );
        }
    }

    # Returns [Queue]
    public static function create (array $elements = [])
    {
        // Returning the value
        return new Queue( $elements );
    }



    # Returns [void]
    public function enqueue ($value)
    {
        // (Pushing the value)
        $this->q->enqueue( $value );
    }

    # Returns [mixed]
    public function dequeue ()
    {
        // Returning the value
        return $this->q->dequeue();
    }



    # Returns [int]
    public function count ()
    {
        // Returning the value
        return $this->q->count();
    }

    # Returns [bool]
    public function is_empty ()
    {
        // Returning the value
        return $this->count() === 0;
    }



    # Returns [mixed]
    public function fetch_head ()
    {
        // Returning the value
        $this->q->bottom();
    }

    # Returns [mixed]
    public function fetch_tail ()
    {
        // Returning the value
        $this->q->top();
    }



    # Returns [void]
    public function process (callable $handle_entry)
    {
        while ( !$this->is_empty() )
        {// Processing each entry
            if ( $handle_entry( $this->dequeue() ) === false ) break;
        }
    }



    # Returns [array<mixed>]
    public function to_array ()
    {
        // (Setting the value)
        $elements = [];



        // (Rewinding the pointer)
        $this->q->rewind();



        while ( $this->q->valid() )
        {// Processing each entry
            // (Appending the value)
            $elements[] = $this->q->current();



            // (Incrementing the pointer)
            $this->q->next();
        }



        // (Rewinding the pointer)
        $this->q->rewind();



        // Returning the value
        return $elements;
    }
}



?>