<?php
namespace PhpCache;

use InvalidArgumentException;

require_once 'CacheStrategy.php';

class Cache implements CacheStrategy
{
	private $strategy;
	
	private $strategyName;
	
	private static $map = array(	'LRU'	=>	'LeastRecentlyUsed',
									'LFU'	=>	'LeastFrequentlyUsed',
									'MRU'	=>	'MostRecentlyUsed' );
	
	public function __construct($strategy = 'LRU', $maxSize = 10, $algorithm = 'sha1')
	{
		if (!isset(self::$map[$strategy])) {
			throw new InvalidArgumentException();
		}
		
		$this->strategyName = $strategy;
		$class = self::$map[$strategy];
		
		require_once "strategies/$class.php";
		$class = '\PhpCache\\'.$class;
		$this->strategy = new $class($maxSize, $algorithm);
	}
	
	public function getStrategy()
	{
		return $this->strategyName;
	}
	
	public function add($key, $value)
	{
		$this->strategy->add($key, $value);
	}
	
	public function get($key)
	{
		return $this->strategy->get($key);
	}
	
	public function getCount()
	{
		return $this->strategy->getCount();
	}
	
	public function getAlgorithm()
	{
		return $this->strategy->getAlgorithm();
	}
	
	public function getMaxSize()
	{
		return $this->strategy->getMaxSize();
	}
	
	public function __toString()
	{
		return $this->strategy->__toString();
	}
}