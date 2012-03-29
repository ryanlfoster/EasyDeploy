# Events

The event handling systems sends a few events to the EventDispatcher 

* **config**<br/>
With this event we send an ConfigEvent which contains the Config object for extending the config before running
* **pre.run**<br/>
This event is send after the configuration is loaded and before we started doing anything. 
* **post.run**<br/>
This event is send after we are finished doing al the upload work to the remote servers 
* **pre.upload**<br/>
With this event we send an ServerEvent and is executed before we start uploading and after we connect to the remote server. This event should be fired if custom servers are implemented.
* **post.upload**<br/>
With this event we send an ServerEvent and is executed after we finished uploading and before we disconnect from the remote server. This event should be fired if custom servers are implemented.

## Listners

We have the following build in events inside the default deployment

* [LocalExecute](#LocalExecute)
* [RemoteExecute](#RemoteExecute)
* [MailZend](#MailZend)

<a name="LocalExecute"></a>
### LocalExecute


<a name="RemoteExecute"></a>
### RemoteExecute


<a name="MailZend"></a>
### MailZend


## Events

The following event objects are integrated

* [ConfigEvent](#ConfigEvent)
* [ServerEvent](#ServerEvent)

<a name="ConfigEvent"></a>
### ConfigEvent

<a name="ServerEvent"></a>
### ServerEvent