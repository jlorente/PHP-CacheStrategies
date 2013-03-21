<?php
namespace PhpCache;

interface CacheStrategy
{
	public function get($key);
	public function add($key, $value);
	public function getCount();
	public function getAlgorithm();
	public function getMaxSize();
}