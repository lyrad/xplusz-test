<?php
namespace AppBundle\Entity;


class TwitterSearch
{
	
	private $q;

	private $count;

	private $result_type;

	public function __construct($q = '', $count = 15, $result_type = 'recent')
	{
		$this->q = $q;
		$this->count = $count;
		$this->result_type = $result_type;
	}

	public function __get($attribute)
	{
		return $this->$attribute;
	}

	public function __set($attribute, $value)
	{
		$this->$attribute = $value;
	}		
}
