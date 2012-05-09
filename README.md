# README

## What is EasyDeploy
---------------------

EasyDeploy is a PHP 5.3 command line tool for automating deployment process to multiple servers. It can be used as a **git** or **svn** hook for automated deployment after a push/commit to a branch on a server. With this tool you can auto upload al the **changes** on a project to an external server. The configuration is handled on the central git or svn server so developers don't need to know the passwords of any server or deployment. The default release comes with FTP and SFTP servers integrated inside the system. With the event listeners its possible to execute local and remote commands (SFTP) or send an e-mail during any part of the deployment process.

## How to EasyDeploy
--------------------

[Installation guide](EasyDeploy/blob/master/doc/installation.md)

[Configuration guide](EasyDeploy/blob/master/doc/configuration.md)

## Command line git ftp
-----------------------

Not documented yet

	git ftp master version -s server


### NOTE 
The SVN deployment isn't ready yet