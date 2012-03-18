<?php

/**
 * @author Ragnis Armus <ragnis.armus@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html The MIT License
 * @todo Handle quitting
 */
class Phergie_Plugin_Logger extends Phergie_Plugin_Abstract
{
	protected $defaultConfig = array(
		'logger.path' => '/tmp/logger',
		'logger.format.entry' => '{DATE} <{NICK}> {MSG}',
		'logger.format.quit' => '{DATE} {NICK} has just quit',
		'logger.format.join' => '{DATE} {NICK} has just joined',
		'logger.format.kick' => '{DATE} {NICK} was kicked',
		'logger.format.date' => 'Y-m-d H:i:s'
	);

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
		$entry = $this->makeEntry($this->getConf('logger.format.entry'), array(
			'{CHANNEL}' => $this->getEvent()->getSource(),
			'{DATE}' => date($this->getConf('logger.format.date')),
			'{MSG}' => $this->plugins->message->getMessage(),
			'{NICK}' => $this->event->getNick()
		));

		$this->writeLog($this->getEvent()->getSource(), $entry);
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

		$entry = $this->makeEntry($this->getConf('logger.format.quit'), array(
			'{CHANNEL}' => $this->getEvent()->getSource(),
			'{DATE}' => date($this->getConf('logger.format.date')),
			'{NICK}' => $this->event->getNick()
		));

		$this->writeLog($channel, $entry);
	}

	/**
	 * When gets kicked.
	 *
	 * @return void
	 */
	public function onKick ()
	{
		$entry = $this->makeEntry($this->getConf('logger.format.kick'), array(
			'{CHANNEL}' => $this->getEvent()->getSource(),
			'{DATE}' => date($this->getConf('logger.format.date')),
			'{NICK}' => $this->event->getNick()
		));

		$this->writeLog($this->getEvent()->getSource(), $entry);
	}

	/**
	 * When the topic is viewed or changed
	 *
	 * @todo implement this
	 * @return void
	 */
	public function onTopic ()
	{
	}

	/**
	 * When someone joins.
	 *
	 * @return void
	 */
	public function onJoin ()
	{
		$entry = $this->makeEntry($this->getConf('logger.format.join'), array(
			'{CHANNEL}' => $this->getEvent()->getSource(),
			'{DATE}' => date($this->getConf('logger.format.date')),
			'{NICK}' => $this->event->getNick()
		));

		$this->writeLog($this->getEvent()->getSource(), $entry);
	}

	/**
	 * Make entry
	 *
	 * @return string
	 */
	protected function makeEntry ($entry, $data)
	{
		return str_replace(array_keys($data), array_values($data), $entry);
	}

	/**
	 * Get path to the log file.
	 */
	protected function getLogFile ($channel)
	{
		return $this->getConf('logger.path') . '/' . $channel . '_' . date('Y-m-d') . '.log';
	}

	/**
	 * Append a line to the log.
	 *
	 * @param string $channel
	 * @param string $entry
	 * @return void
	 */
	protected function writeLog ($channel, $entry)
	{
		$channel = str_replace('#', '', $channel);
		$fh = fopen($this->getLogFile($channel), 'a');
		fwrite($fh, $entry . "\n");
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

