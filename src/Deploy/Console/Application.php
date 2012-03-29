<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
	public function __construct()
    {
        parent::__construct('EasyDeploy', '1.0.0');
    }
	
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
		$this->add(new Command\GitCommand());
		$this->add(new Command\SvnCommand());
	}
	
}
   