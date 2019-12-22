<?php
namespace Braunson\FatSecret;
class NonceFactory
{
    /**
     * Get a fresh nonce based on PHP md5 and uniqueid functions.
     *
     * @return int
     */
    public function get()
    {
        return md5(uniqid());
    }
}
