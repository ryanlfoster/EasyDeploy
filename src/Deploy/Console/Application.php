<?php

namespace Deploy\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deploy\Command;

/**
 * The console application that handles the commands
 *
 * @author Cliff Odijk <cliff@obro.nl>
 */
class Application extends BaseApplication
{
	/**
 	 * {@inheritDoc}
 	 */
	public function doRun(InputInterface $input, OutputInterface $output)
	{
		$this->registerCommands();
		return parent::doRun($input, $output);
	}
	
	/**
 	 * Initializes all the composer commands
 	 */
	protected function registerCommands()
	{
		$this->add(new Command\UploadCommand());
	}
	
}
   