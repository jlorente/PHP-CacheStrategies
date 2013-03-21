<?php
namespace PhpCache;

use Exception, InvalidArgumentException;

require_once realpath(dirname(__FILE__).'/../AbstractCache.php');
require_once realpath(dirname(__FILE__).'/../Node.php');

class LeastRecentlyUsed extends AbstractCache
{
	protected $head;
	
	protected $tail;
	
	public function __construct($maxSize = 10, $algorithm = 'sha1')
	{
		parent::__construct($maxSize, $algorithm);
		
		$this->head = new Node(null);
		$this->tail = new Node(null);
		$this->count = 0;
		$this->head->setNext($this->tail);
		$this->tail->setPrev($this->head);
	}
	
	public function get($key)
	{
		$func = $this->algorithm;
		$hashedKey = $func($key);
		if (!isset($this->hashTable[$hashedKey])) {	
			throw new Exception('Key isn\'t store in cache');
		}
		
		$node = $this->hashTable[$hashedKey];
		$node->hit();
		$node->getPrev()->setNext($node->getNext());
		$node->getNext()->setPrev($node->getPrev());
		
		$node->setPrev($this->tail->getPrev());
		$node->setNext($this->tail);
		$this->tail->setPrev($node);

		return $node->getValue();
	}
	
	public function add($key, $value)
	{
		if ($this->check($key)) {
			return $this->get($key);
		}
		
		$func = $this->algorithm;
		$node = new Node($value);;
		$node->setKey($func($key));	
		if ($this->count == $this->maxSize) {
			$this->dequeue();
		}
		$this->enqueue($node);
	}
	
	protected function dequeue()
	{
		unset($this->hashTable[$this->head->getNext()->getKey()]);
		$this->head->getNext()->remove();		
		$this->count--;
	}
	
	protected function enqueue(Node $node)
	{
		$this->tail->getPrev()->setNext($node);
		$node->setPrev($this->tail->getPrev());
		$node->setNext($this->tail);
		$this->tail->setPrev($node);
		$this->hashTable[$node->getKey()] = $node;
		$this->count++;
	}
	
	public function __toString()
	{
		$return = '';
		$node = $this->head->getNext();
		
		while ($node !== $this->tail) {
			$return .= '['.$node->getKey().' | LastHit: '.$node->getLastHitDate()->format('Y-m-d H:i:s').' ]: '.$node->getValue().PHP_EOL;
			$node = $node->getNext();
		}
		return $return;
	}
}