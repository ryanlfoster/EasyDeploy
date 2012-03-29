# Servers

There are a 3 pre defined servers inside of the system, al the servers require an "_type" variable this type is a string of an class name that is used to upload the files to any destination. This class should implement the Deploy\Server\ServerInterface interface for deploying its source. The rest of the variables are send to the server objects inside of the __construct function. You can specify any "extra" variables you like to use for instance inside of the event handling process. 

* [FTP](#ftp)
* [SFTP](#sftp)
* [Group](#group)

<a name="ftp"></a>
## FTP
Default FTP server doesn't support RemoteExecute listener but can be used to upload to almost any server.

	``` json
	{
		"_type"			: "Deploy/Server/Ftp",
		"server"		: "demodomain.com",
		"port"			: 21,					// Optional
		"username"		: "demo",
		"password"		: "login",
		"path"			: "/path/on/remote"
	}
	```

<a name="sftp"></a>
## SFTP
The SFTP server requires the "_auth" variable to define which authentication method is to be used during the connection. With the SFTP connection the RemoteExecute listener can execute command before and after running its upload process.

	``` json
	{
		"_type"			: "Deploy/Server/Sftp",
		"_auth"			: "password",			// List(none, password, pubkey)
		"server"		: "demodomain.com",
		"port"			: 22,					// Optional
		"username"		: "demo",
		"password"		: "login",				// Required for _auth:password or _auth:pubkey as passphrase
		"privkeyfile"	: "/path/to/key"		// Required for _auth:pubkey
	}
	```

<a name="group"></a>
## Group
If an project needs to me deployed over multiple servers we can define multiple servers in the deploy configuration. But maybe the deployment server is a list of servers and we want to create a group deployment. The list of servers can be any type of servers in this example we use FTP and SFTP at the same time to different servers.

	``` json
	{
		"_type"		: "Deploy/Server/Group",
		"servers"	: [
			{
				"_type"			: "Deploy/Server/Ftp",
				"server"		: "192.168.123.4",
				"username"		: "demo",
				"password"		: "login",
				"path"			: "/path/on/remote"
			},
			{
				"_type"			: "Deploy/Server/Sftp",
				"_auth"			: "password",
				"server"		: "192.168.123.5",
				"username"		: "demo",
				"password"		: "login"
			}
		]
	}
	```

