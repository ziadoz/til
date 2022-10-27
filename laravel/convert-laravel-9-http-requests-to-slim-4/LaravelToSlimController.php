<?php
use Illuminate\Foundation\Application;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Http\Response as SlimResponse;
use Slim\Http\ServerRequest as SlimServerRequest;

class LaravelToSlimController
{
    public function __invoke(LaravelRequest $laravelRequest)
    {
        $slim = (require '/path/to/slim.php')();

        return $this->createLaravelResponse(
            $slim->handle($this->createSlimRequest($laravelRequest))
        );
    }

    protected function createSlimRequest(LaravelRequest $laravelRequest): SlimServerRequest
    {
        $slimRequest = ServerRequestCreatorFactory::create()
            ->createServerRequestFromGlobals()
            ->withCookieParams($laravelRequest->cookies->all())
            ->withQueryParams($laravelRequest->query())
            ->withParsedBody($laravelRequest->post());

        foreach ($laravelRequest->headers->all() as $name => $value) {
            $slimRequest = $slimRequest->withHeader($name, $value);
        }

        return $slimRequest;
    }

    protected function createLaravelResponse(SlimResponse $slimResponse): LaravelResponse
    {
        return new LaravelResponse(
            (string) $slimResponse->getBody(),
            $slimResponse->getStatusCode(),
            $slimResponse->getHeaders(),
        );
    }
}
