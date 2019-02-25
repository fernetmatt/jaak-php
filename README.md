# jaak-php

##### A simple port of jaak.js made with love by Lucid Tunes
##### This Project is under active development

### Introduction
This PHP library is intended to work within jaak.js frontend interface.

The main functionality of this library is to register frontend devices
(which run jaak.js or other frontend library).

The side feature is metadata mining in order to provide to your backend's
clients a rich experience such: albums grouping and classification,
statistics, new releases etc. etc., unloading the client from this
"supply-chain" work. In this way you can off-load some code from
your clients implementation, keeping the central logic in your backend.

### Intallation

```
composer require lucidtunes/jaak
```


### Device Registration
Your backend should ideally listens for a */registerDevice [POST]* request
coming from your users. When this happens you should make sure the user
that made the request is fully authenticated on your system, and only
after you are sure you leverage this library methods to register the
user's device.

Following [Jaak official documentation](https://github.com/jaakmusic/jaak.js) in order to register a new device onto Jaak platform, to be able to retrieve and listen to music content, you should complete the following steps:

##### Frontend Side
```js
const deviceKey = await Playback.Key.generate();
const deviceRegistration = {
    key: await deviceKey.toJWK(),
    name: 'your-custom-device-name',
  };
let response = await fetch(
 `${your-backend-endpoint}/registerDevice`, {
   body: JSON.stringify(deviceRegistration),
   headers: new Headers({
     'Accept': 'application/json',
     'Content-Type': 'application/json',
   }),
   method: 'POST'
 }
);

// now you can start using your previously generated deviceKey to talk directly to Jaak
```

##### Backend Side
```composer
composer require lucidtunes/jaak
```
```php
// Lumen
$router->post('registerDevice', function(Illuminate\Http\Request $request) use ($router) {
    
    // authenticate User
    // ...
    $userId = $request->user()->id;
    $deviceJson = $request->json()->all();
    $deviceJson['consumerId'] = $userId;
    
    $jaakDevice = Lucid\Jaak\Device::createFromJson(json_encode($deviceJson);
    $appKey = Lucid\Jaak\Key::createFromJWK(file_get_contets('app-key-jaak.json'));
    $application = Lucid\Jaak\Application::create($appKey);
    try {
        $jaakDevice = $application->registerDevice($jaakDevice);
        // device is registered
    } catch (\Exception $e) {
        //
    }
    
    return;
});
```



### Retrieve metadata
Creating an *Application* object instance using a specific AppKey (downloaded from *beta.jaak.io*) and using the *Application::listTracks()* method you can retrieve the current tracks available to your Jaak Application

```php
$appKey = Lucid\Jaak\Key::createFromJWK(file_get_contets('app-key-jaak.json'));
$application = Lucid\Jaak\Application::create($appKey);
$tracks = $application->listTracks();

/** @var \stdClass $track */
foreach ($tracks as $track) {
    echo 'Track title: ' . $track->data()->title;
}
```

### Testing

```
vendor/phpunit/phpunit/phpunit --bootstrap vendor/autoload.php test/
```