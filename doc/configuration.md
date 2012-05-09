# Configuration

The configuration is handled by creating a deploy.json the configuration can be located at the following locations. The configurations is written in JSON format.

There can be a deploy.json inside of the root of de folder you are running the deploy command from.

**git**: If you use git you can place the deploy.json inside the .git/config directory on the server

**svn**: If you use svn you can place the deploy.json inside the <repodir>/conf directory on the server 

## Deploy

A deployment is a selection of a branch inside the repository that is being deployed to an external server. The "deploy" variabel is not required and the keys of the object inside it match the branch names inside of the repositorys. The values of the object are strings or an list of strings wraped inside a array like this:


	{
		"deploy" : {
			"master"		: "server1",
			"development"	: "server2",
			"special"		: ["server1", "server"]
		}
		
	}
	
## Servers

The servers variable is the section to specify the different available servers to deploy the branch. The "servers" variable is required and a configuration looks something like this.

	{
		"servers" : {
			"server1" : {
				"_type"			: "Deploy/Server/Ftp",
				"server"		: "demodomain.com",
				"username"		: "demo",
				"password"		: "login",
				"path"			: "/path/on/remote"
			},
			"server2" : {
				"_type"			: "Deploy/Server/Sftp",
				"_auth"			: "password",
				"server"		: "demodomain.com",
				"username"		: "demo",
				"password"		: "login"
			}
		}
	}
	
[Server details](servers.md)

## Events

The events variable is the section where you can specify different event listeners to handle stuff during the process. The build in events are listed on the detail page of the events. 

	{
		"events" : {
			"post.upload" : [
				{
					"_type"		: "Deploy/Listner/LocalExecute",
					"command"	: "/usr/local/bin/php {{ path }}/bin/console cache:clear"
				}
			]
		}
	}

[Event details](events.md)

