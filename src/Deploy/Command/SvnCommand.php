<?php

/*
 * This file is part of the Deploy package.
 *
 * (c) Cliff Odijk <cliff@obro.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deploy\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Command\Command;
use Deploy\Reader\Svn;
use Deploy\Deploy;

/**
 * SvnCommand helps uploading diffs to a server.
 *
 * @author Cliff Odijk <cliff@obro.nl>
 */
class SvnCommand extends Command
{
	private $command;

	/**
 	 * {@inheritdoc}
 	 */
	protected function configure()
	{
		$this
			->setName('svn')
			->setDefinition(array(
				new InputArgument('repos', InputArgument::REQUIRED, 'The folder to the repo'),
				new InputArgument('rev', InputArgument::REQUIRED, 'The revision number'),
			))
			->setDescription('Uploads changes of svn repo to a server')
			->setHelp("The <info>%command.name%</info> uploads changes made on a repository to anny server");
		;
	}
	
	/**
 	 * {@inheritdoc}
 	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/**
		 * Define the reader based on input
		 */
			$reader = new Svn();
			$reader->isRepo();
			$reader->setVersion($input->getArgument('rev'));
		
		/**
		 * Start deploying
		 */
			$deploy = new Deploy($reader, $output);
			$deploy->run();
	}
}
