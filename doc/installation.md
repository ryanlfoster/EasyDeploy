# Installation

## Step 1 ) Download the project

	``` sh
	git clone https://github.com/cmodijk/EasyDeploy.git
	```

## Step 2 ) Download vendors using composer

	``` sh
	wget http://getcomposer.org/composer.phar
	
	php composer.phar install
	```

## Step 3 ) Create the deploy.json

[Configuration guide](/blob/master/doc/configuration.md)

## Step 4 ) Install the hook

### git

Create the following script in the .git/hooks/post-receive file

	``` sh
	#!/bin/bash
	
	while read OLD_COMMIT NEW_COMMIT REFNAME; do	
		BRANCH=${REFNAME#refs/heads/}
		$(dirname $(readlink -f "$0"))/deploy/console "$BRANCH" "$NEW_COMMIT" || exit $?
	done
	true
	```
## svn

NOT FINISHED DONT USE!