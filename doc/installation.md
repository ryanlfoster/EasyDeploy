# Installation

## Step 1 ) Download the project

	git clone https://github.com/cmodijk/EasyDeploy.git

## Step 2 ) Download vendors using composer

	wget http://getcomposer.org/composer.phar
	
	php composer.phar install

## Step 3 ) Create the deploy.json

[Configuration guide](configuration.md)

## Step 4 ) Install

You can use EasyDeploy by using the git hooks or use the git ftp plugin

### Plugin )

Install the bin path to your profile.

	nano ~/.bash_profile 
	
	PATH=#PathToEasyDeploy#/EasyDeploy/bin:$PATH

You can use the command 

	Usage:
	 git git [-s|--server[="..."]] [-a|--all] [branch] [new]
	
	Optional arguments:
	 branch        The branche name
	 new           The new version number
	
	Options:
	 --server (-s) The server name to deploy to
	 --all (-a)    Deploy to all servers


### Hooks )

#### git

Create the following script in the .git/hooks/post-receive file

	#!/bin/bash
	
	while read OLD_COMMIT NEW_COMMIT REFNAME; do	
		BRANCH=${REFNAME#refs/heads/}
		$(dirname $(readlink -f "$0"))/deploy/console "$BRANCH" "$NEW_COMMIT" || exit $?
	done
	true
	
#### svn

NOT FINISHED DONT USE!