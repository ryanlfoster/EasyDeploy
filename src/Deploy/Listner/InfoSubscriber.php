<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Deploy\Listner;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Deploy\Event\FileEvent;

class InfoSubscriber implements EventSubscriberInterface
{
	protected $output;
	
	/**
	 * Info register
	 *
	 * @param OutputInterface $output output renderen
	 */
	public function __construct(OutputInterface $output)
	{
		$this->output = $output;
	}
	
	/**
	 * Return the events to register
	 */
	public static function getSubscribedEvents()
	{
		return array(
			"config"		=> "message",
			"pre.run"		=> "message",
			"post.run"		=> "message",
			"pre.upload"	=> "message",
			"post.upload"	=> "message",
			"file.upload"	=> "file",
			"file.delete"	=> "file",
		);
	}
	
	/**
	 * Message event
	 *
	 * @param Event $event show a message based on the events 
	 */
	public function message(Event $event)
	{
		$messages = array(
			"config"		=> "Extend the Config object",
			"pre.run"		=> "Start met de deployment",
			"post.run"		=> "Deployment afgerond",
			"pre.upload"	=> "Start uploading",
			"post.upload"	=> "Finish uploading",
		);
		
		if (isset($messages[$event->getName()])){
			$this->output->writeln($messages[$event->getName()]);
		}
	}
	
	/**
	 * Message event
	 *
	 * @param Event $event show a message based on the events 
	 */
	public function file(FileEvent $event)
	{
		$messages = array(
			"file.upload"	=> "Upload file ".$event->getFile(),
			"file.delete"	=> "Remove file ".$event->getFile(),
		);
		
		if (isset($messages[$event->getName()])){
			$this->output->writeln($messages[$event->getName()]);
		}
	}
	
}