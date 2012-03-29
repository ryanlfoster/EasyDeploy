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
use Deploy\Reader\Git;
use Deploy\Deploy;

/**
 * GitCommand helps uploading diffs to a server.
 *
 * @author Cliff Odijk <cliff@obro.nl>
 */
class GitCommand extends Command
{
	private $command;

	/**
 	 * {@inheritdoc}
 	 */
	protected function configure()
	{
		$this
			->setName('git')
			->setDefinition(array(
				new InputArgument('branch',		InputArgument::OPTIONAL, 'The branche name'),
				new InputArgument('new',		InputArgument::OPTIONAL, 'The new version number'),
				new InputOption('server', 's',	InputOption::VALUE_OPTIONAL, 'The server name'),
				new InputOption('all', 'a',		InputOption::VALUE_NONE, 'Deploy to all servers'),
			))
			->setDescription('Uploads changes of git branche to a server')
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
			$reader = new Git();
			$reader->isRepo();
			$reader->setVersion($input->getArgument('new'));
			$reader->setBranch($input->getArgument('branch'));
			
		/**
		 * Start deploying
		 */
			$deploy = new Deploy($reader, $output);
			
			// Upload to a specified server
			if ($server = $input->getOption('server')){
				$deploy->server($server);
			
			// Upload to all servers
			} elseif ($input->getOption('all')){
				$deploy->all();
			
			// Upload to the deployment server
			} else {
				$deploy->run();
			}
		/* - */
	}
}
