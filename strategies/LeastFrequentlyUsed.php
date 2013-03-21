<?php
namespace PhpCache;

use Exception, InvalidArgumentException;

require_once realpath(dirname(__FILE__).'/../AbstractCache.php');
require_once realpath(dirname(__FILE__).'/../Node.php');

class LeastFrequentlyUsed extends AbstractCache
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
		
		$this->move($node);
		
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
		$node->hit();
		if ($this->count == $this->maxSize) {
			$this->destack();
		}
		$this->stack($node);
	}
	
	protected function move(Node $node)
	{
		$target = $node->getPrev();
		if ($target !== $this->head && $target->getHitRatio() < $node->getHitRatio()) {
			while ($target !== $this->head && $target->getHitRatio() < $node->getHitRatio()) {
				$target = $target->getPrev();
			}
			
			$node->getPrev()->setNext($node->getNext());
			$node->getNext()->setPrev($node->getPrev());
			
			$target->getNext()->setPrev($node);
			$node->setPrev($target);
			$node->setNext($target->getNext());
			$target->setNext($node);
		}
	}
	
	public function stack(Node $node)
	{
		$this->tail->getPrev()->setNext($node);
		$node->setNext($this->tail);
		$node->setPrev($this->tail->getPrev());
		$this->tail->setPrev($node);
		$this->hashTable[$node->getKey()] = $node;
		$this->count++;
	}
	
	public function destack()
	{
		unset($this->hashTable[$this->tail->getPrev()->getKey()]);
		$this->tail->getPrev()->remove();
		$this->count--;
	}
	
	public function __toString()
	{
		$return = '';
		$node = $this->head->getNext();
		
		while ($node !== $this->tail) {
			$return .= '['.$node->getKey().' | HitRatio: '.$this->getHitRatio().' ]: '.$node->getValue().PHP_EOL;
			$node = $node->getNext();
		}
		return $return;
	}
}