<?php
namespace Braunson\FatSecret;

class NonceFactory
{
	public function get(){
		return md5(uniqid());
	}
}
