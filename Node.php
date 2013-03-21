<?php
namespace PhpCache;

use Datetime, DateTimeZone;

class Node
{
	protected $value;
	protected $next;
	protected $prev;
	protected $key;
	protected $hitRatio = 0;
	protected $lastHitDate = 0;
	
	public function __construct($value, Node $prev = null, Node $next = null)
	{
		$this->setValue($value);
		$this->setPrev($prev);
		$this->setNext($next);
		$this->lastHitDate = new DateTime(null, new DateTimeZone('UTC'));
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function setNext(Node $next = null)
	{
		$this->next = $next;
	}

	public function setPrev(Node $prev = null)
	{
		$this->prev = $prev;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function getNext()
	{
		return $this->next;
	}

	public function getPrev()
	{
		return $this->prev;
	}
	
	public function getKey()
	{
		return $this->key;
	}
	
	public function setKey($key)
	{
		$this->key = $key;
	}
	
	public function hit()
	{
		$this->hitRatio++;
		$this->lastHitDate = new DateTime(null, new DateTimeZone('UTC'));
	}

	public function getLastHitDate()
	{
		return $this->lastHitDate;
	}
	
	public function getHitRatio()
	{
		return $this->hitRatio;
	}
	
	public function __toString()
	{
		return $this->getValue();
	}
	
	public function remove()
	{
		$this->getPrev()->setNext($this->getNext());
		$this->getNext()->setPrev($this->getPrev());
		
		$this->prev = $this->next = $this->value = null;
	}
}