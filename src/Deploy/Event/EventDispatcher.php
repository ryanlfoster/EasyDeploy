<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy\Event;

use Symfony\Component\EventDispatcher\EventDispatcher AS MasterEventDispatcher;
use Symfony\Component\EventDispatcher\Event;

class EventDispatcher extends MasterEventDispatcher
{
	/**
	 * Add an array of listners
	 * 
	 * @param array key/array $baseEvents list of event listners
	 * @param integer  $priority  The higher this value, the earlier an event
	 *                            listener will be triggered in the chain (defaults to 0)
	 */
	public function addListeners($baseEvents, $priority = 0)
	{
		foreach ($baseEvents as $name => $events)
		{
			foreach ($events as $event)
			{
				$this->addListener($name, $event, $priority);
			}
		}
	}
	
	/**
	 * Remove an array of listners
	 *
	 * @param array key/array $baseEvents list of event listners
	 */
	public function removeListeners($baseEvents)
	{
		foreach ($baseEvents as $name => $events)
		{
			foreach ($events as $event)
			{
				$this->removeListener($name, $event);
			}
		}
	}
}