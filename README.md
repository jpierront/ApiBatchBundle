# JPierrontApiBatchBundle

## About

The JPierrontApiBatchBundle allows you to send multiple JSON api call in only one http request.

## Installation

Require the `jpierront/api-batch-bundle` package in your composer.json and update your dependencies.

    $ composer require jpierront/api-batch-bundle

Add the JPierrontApiBatchBundle to your application's kernel:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new JPierront\ApiBatchBundle\JPierrontApiBatchBundle(),
            ...
        );
        ...
    }

## Configuration

Create a route for the batch action

YAML:

    # app/config/routing.yml
    batch:
        path:   /api/batch
        defaults:  { _controller: JPierrontApiBatchBundle:ApiBatch:apiBatch }

Annotation:

    # app/config/routing.yml
    batch:
        resource: AppBundle\Controller\ApiBatchController
        
    // AppBundle\Controller\ApiBatchController.php
    class ApiBatchController extends BaseApiBatchController
    {
        /**
         * @Route(path="batch", name="batch")
         */
        public function getAction(Request $request)
        {
            return parent::getAction($request);
        }
    }

## Usage

### Request

Call your new batch api in POST method with JSON in request body

    [
        {
            "method": "GET",
            "url": "/sub-api-1",
            "parameters": "queryParameter1=value1&queryParameter2=value2"
        },
        {
            "method": "POST",
            "url": "/sub-api-2",
            "parameters": "postParameter1=value1&postParameter2=value2"
        }
    ]

`method`

* Accept any HTTP method (GET, POST, PUT, PATCH, ...)
* Must be equal to the method expected by your api

`url`

* Relative url of your sub api
* Without the hostname
* Without the query parameters

`parameters`

* You GET or POST parameters formatted like query string

### Response

The batch api will return a JSON response

    [
        {
            "code": 200,
            "headers": {
                "content-type": "application\/json",
            },
            "body": "Your json response of sub api 1"
        },
        {
            "code": 200,
            "headers": {
                "content-type": "application\/json",
            },
            "body": "Your json response of sub api 2"
        }
    ]

`code`

* Is the http status code of the sub api call

`headers`

* Is the headers of the sub api call

`body`

* Is the body of the sub api call

## License

Released under the MIT License, see LICENSE.
