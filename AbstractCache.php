<?php
namespace PhpCache;

use Exception, InvalidArgumentException;

require_once 'CacheStrategy.php';

abstract class AbstractCache implements CacheStrategy
{
	protected $algorithm;
	
	protected $hashTable;
	
	protected $maxSize;
	
	protected $count;
		
	public function __construct($maxSize = 10, $algorithm = 'sha1')
	{
		$this->hashTable = array();
		$this->setMaxSize($maxSize);
		$this->setAlgorithm($algorithm);
	}
		
	protected function setAlgorithm($algorithm)
	{
		if (is_callable($algorithm, false)) {
			$this->algorithm = $algorithm;
		} elseif (in_array($algorithm, hash_algos())) {
			$this->algorithm = function($key) use ($algorithm) { return hash($algorithm, $key); };
		} else {
			throw new Exception('Invalid algorithm for create index');
		}
	}
	
	public function getAlgorithm()
	{
		return $this->algorithm;
	}
	
	public function setMaxSize($maxSize)
	{
		if (!is_numeric($maxSize)) {
			throw new InvalidArgumentException('Invalid max size given');
		}
		$this->maxSize = $maxSize;
	}
	
	public function getMaxSize()
	{
		return $this->maxSize;
	}
	
	public function getCount()
	{
		return $this->count;
	}
	
	public function check($key)
	{
		$func = $this->algorithm;
		return isset($this->hashTable[$func($key)]);
	}
}