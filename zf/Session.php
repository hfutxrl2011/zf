<?php

namespace zf;

use Exception;

class Session
{
	public function __construct()
	{
		if(PHP_SESSION_DISABLED == session_status())
		{
			throw new Exception('sessions are disabled');
		}
		$this->start();
		session_write_close();
	}

	public function start()
	{
		if(PHP_SESSION_ACTIVE != session_status()) session_start();
	}

	public function __get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	public function __set($key, $value)
	{
		$this->start();
		$_SESSION[$key] = $value;
		session_write_close();
	}

	public function set($values)
	{
		$this->start();
		foreach($values as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
		session_write_close();
	}

	public function destroy()
	{
		$this->start();
		return session_destroy();
	}
}
