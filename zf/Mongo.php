<?php

namespace zf;

class Mongo implements \ArrayAccess
{

	private $_config;
	private $_cachedConnections = [];

	public function __construct($config)
	{
		$this->_config = $config;
	}

	private function _rewriteConfig($config)
	{
		$ret = [];
		$collections = [];
		foreach($config as $collection=>$conf)
		{
			if(is_int($collection))
			{
				$collections[] = $conf;
			}
			else
			{
				while($collections) $ret[array_pop($collections)] = $conf;
				$ret[$collection] = $conf;
			}
		}
		return $ret;
	}

	public function __get($collection)
	{
		if(empty($this->_config[$collection]))
		{
			$this->_config = $this->_rewriteConfig($this->_config);
			if(empty($this->_config[$collection]))
			{
				throw new \Exception("collection \"$collection\" not defined");
			}
		}

		$config = $this->_config[$collection];
		if(empty($this->_cachedConnections[$config['url']]))
		{
			$this->_cachedConnections[$collection] = isset($config['options'])
				? new \MongoClient($config['url'], $config['options'])
				: new \MongoClient($config['url']);
		}
		return $this->$collection = $this->_cachedConnections[$collection]
			->selectDB($config['database'])
			->selectCollection($collection);
	}

	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	public function offsetExists($offset) { }
	public function offsetSet($offset, $value) { }
	public function offsetUnset($offset) { }
}
