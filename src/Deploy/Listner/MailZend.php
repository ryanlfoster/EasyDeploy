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

use Symfony\Component\EventDispatcher\Event;

class MailZend extends ListnerAbstract
{	
	public function execute(Event $event)
	{
		//print_r($event);
	}
	
}