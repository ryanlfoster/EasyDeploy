<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
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

use Deploy\Json\JsonFile;
use Deploy\Readers;

/**
 * HelpCommand displays the help for a given command.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class UploadCommand extends Command
{
	private $command;

	/**
 	 * {@inheritdoc}
 	 */
	protected function configure()
	{
		$this
			->setName('upload')
			->setDefinition(array(
				new InputArgument('branche', InputArgument::OPTIONAL, 'The branche name', 'master'),
				new InputArgument('old', InputArgument::OPTIONAL, 'The old version number', ''),
				new InputArgument('new', InputArgument::OPTIONAL, 'The new version number', ''),
			))
			->setDescription('Uploads changes of branche to server')
			->setHelp("The <info>%command.name%</info> uploads changes made on a repository to anny server");
		;
	}
	
	/**
 	 * {@inheritdoc}
 	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln($input->getArgument('branche'));
		
		$file = new JsonFile("deploy.json");
		
		print_r($file->read());
		print_r($file->validateSchema());
		//
		
		$reader = new Readers\Git();
		
		$reader->isRepo();
		
		
		echo "\n";
		
	}
}
