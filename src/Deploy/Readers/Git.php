<?php

namespace Deploy\Readers;

use Symfony\Component\Process\Process;

class Git
{

	

	public function isRepo()
	{
		$process = new Process('git rev-parse --git-dir');
		$process->run();
		if (!$process->isSuccessful()) {
			throw new \RuntimeException($process->getErrorOutput());
		}
		return true;
	}

}