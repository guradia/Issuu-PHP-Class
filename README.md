Issuu-PHP-Class
===============

### Description
An advanced but easy to use client for the Issuu API. By the way… The Issuu API really could use some tweaks, as it requires alot of operations for the simplest of tasks. Some logic in the setup would be great! Anyhow, this client makes life a bit easier.

### Usage
This client uses one/uno/ein/en/um class for managing the connections between the API and the client. To run a simple query against the API, you could do this:

	use Issuu\Client;
	$client = new Client($apiKey, $apiSecret);
	
	$client->setOptions(array(
			'action' => 'issuu.documents.list',
			'format' => 'json',
			'responseParams' => 'name,publishDate',
			'responseType' => 'slim' // 'full' = Everything, 'slim' = Only the good stuff.
	));
	
	$response = $client->request();
	
	foreach ($response as $document) {
		echo $document->name." was published ".date("m-d-Y",strtotime($document->publishDate));
	}
	
	echo $response->name;
	
__Post__ parameters are given in the `request(array[post])` method.

	use Issuu\Client;
	$client = new Client($apiKey, $apiSecret);
	
	$client->setOptions(array(
			'action' => 'issuu.folder.add',
			'format' => 'json'
	));
	
	$response = $client->request(array("folderName" => "Testfolder"));
	
	print_r($response);

### Adapters
The adapters are providing easier access to the most used functions in the Issuu API. For example, to get the latest document in a specific folder, you can initiate the "Documents" Adapter. In it is a function called `getLatestInFolder(string[folderName])`. See the example below:

	$client = new Client($apiKey, $apiSecret);
	$adapter = $client->adapter("Documents");
	
	$latest = $adapter->getLatestInFolder("Testfolder");
	
	echo $latest->name;
	
There are 3 Adapters currently available. `Documents`, `Folders` and `Bookmarks`. These classes can easily be modified with your own hooks and functions.

### Requirements
* PHP 5.4
* cURL