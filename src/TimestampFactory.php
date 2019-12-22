<?php
namespace Braunson\FatSecret;
class TimestampFactory
{
    /**
     * Get a timestamp by using the PHP time funcion.
     *
     * @return int
     */
    public function get()
    {
        return time();
    }
}
