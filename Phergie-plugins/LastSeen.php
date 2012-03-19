<?php

/**
 * @author Ragnis Armus <ragnis.armus@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html The MIT License
 */
class Phergie_Plugin_LastSeen extends Phergie_Plugin_Abstract
{
	protected $defaultConfig = array(
		'lastseen.datapath' => '/tmp/lastseen'
	);

	protected $isLoaded = array();
	protected $data = array();

	/**
	 * @return void
	 */
	public function onLoad ()
	{
		$this->getPluginHandler()->getPlugin('Message');
	}

	/**
	 * When someone says something.
	 *
	 * @return void
	 */
	public function onPrivmsg ()
	{
		$channel = $this->getEvent()->getSource();
		$nick = $this->event->getNick();
		$msg = $this->plugins->message->getMessage();

		if (preg_match('/^last_seen ([^ ]+)/', $msg, $match))
		{
			$qnick = $match[1];
			$response = array();

			if (!isset($this->data[$channel]) ||
				!isset($this->data[$channel][$qnick]) ||
				(
					!isset($this->data[$channel][$qnick]['online']) &&
					!isset($this->data[$channel][$qnick]['privmsg'])
				))
			{
				return;
			}

			if (isset($this->data[$channel][$qnick]['online']))
			{
				$response[] = $qnick . ' was last online at ' .
					gmdate('Y-m-d H:i', $this->data[$channel][$qnick]['online']);
			}

			if (isset($this->data[$channel][$qnick]['privmsg']))
			{
				$response[] = $qnick . ' last spoke at ' .
					gmdate('Y-m-d H:i',$this->data[$channel][$qnick]['privmsg']);
			}

			foreach ($response as $line)
			{
				$this->doPrivmsg($channel, $line);
			}
		}

		$this->setData($channel, $nick, 'privmsg', time());
		$this->writeData($channel);
	}

	/**
	 * When someone quits.
	 *
	 * @return void
	 */
	public function onPart ()
	{
		$explode = explode(' ', $this->getEvent()->getRawData());
		$channel = $explode[1];
		$nick = $this->event->getNick();		

		$this->setData($channel, $nick, 'online', time());
		$this->writeData($channel);
	}

	/**
	 * When gets kicked.
	 *
	 * @return void
	 */
	public function onKick ()
	{
		return $this->onPart();
	}

	/**
	 * When someone joins.
	 *
	 * @return void
	 */
	public function onJoin ()
	{
		$channel = $this->getEvent()->getSource();
		$nick = $this->event->getNick();		

		$this->setData($channel, $nick, 'online', time());
		$this->writeData($channel);
	}

	/**
	 * @param string $nick
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	protected function setData ($channel, $nick, $key, $value)
	{
		if (!isset($this->data[$channel]))
		{
			$this->data[$channel] = array();
		}

		if (!isset($this->data[$channel][$nick]))
		{
			$this->data[$channel][$nick] = array();
		}

		$this->data[$channel][$nick][$key] = $value;
	}

	/**
	 * Get path to the data file.
	 *
	 * @param string $channel
	 * @return string
	 */
	protected function getDataFile ($channel)
	{
		return $this->getConf('lastseen.datapath') . '/' . $channel .
			'_lastseen';
	}

	/**
	 * Load data from data file.
	 *
	 * @param string $channel
	 */
	protected function loadData ($channel)
	{
		if (!file_exists($this->getDataFile($channel)))
		{
			return;
		}

		$raw = file_get_contents($this->getDataFile($channel));
		$lines = explode("\n", $raw);

		if (!isset($this->data[$channel]))
		{
			$this->data[$channel] = array();
		}

		foreach ($lines as $line)
		{
			list($nick, $data) = explode('|', $line);
			$this->data[$channel][$nick] = json_decode($data, true);
		}

		$this->isLoaded[] = $channel;
	}

	/**
	 * Write data to file
	 *
	 * @param string $channel
	 * @return void
	 */
	protected function writeData ($channel)
	{
		if (!in_array($channel, $this->isLoaded))
		{
			$this->loadData($channel);
		}

		if (!isset($this->data[$channel]))
		{
			return;
		}

		$raw = array();

		foreach ($this->data[$channel] as $nick => $data)
		{
			$raw[] = $nick . '|' . json_encode($data);
		}

		if (!file_exists(dirname($this->getDataFile($channel))))
		{
			mkdir(dirname($this->getDataFile($channel)), 0777, true);
		}

		$fh = fopen($this->getDataFile($channel), 'w');
		fwrite($fh, implode("\n", $raw));
		fclose($fh);
	}

	/**
	 * Get a config value.
	 *
	 * @return mixed
	 */
	protected function getConf ($key)
	{
		if (isset($this->config[$key]))
		{
			return $this->config[$key];
		}

		if (isset($this->defaultConfig[$key]))
		{
			return $this->defaultConfig[$key];
		}

		return null;
	}
}

