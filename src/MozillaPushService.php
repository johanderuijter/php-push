<?php declare(strict_types = 1);

namespace Cmnty\Push;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class MozillaPushService implements PushService
{
    /**
     * Check weather this push service supports a certain endpoint.
     *
     * @param Endpoint $endpoint
     *
     * @return bool
     */
    public function supportsEndpoint(Endpoint $endpoint): bool
    {
        return 'https://updates.push.services.mozilla.com/wpush/v1' === substr($endpoint->getUrl(), 0, 50);
    }

    /**
     * Create a push request.
     *
     * @param PushMessage $message
     *
     * @return RequestInterface
     */
    public function createRequest(PushMessage $message): RequestInterface
    {
        $request = new Request(
            'POST',
            $this->getUrl($message),
            $this->getHeaders($message),
            $this->getBody($message)
        );

        return $request;
    }

    /**
     * Get the request url
     *
     * @param PushMessage $message
     *
     * @return string
     */
    private function getUrl(PushMessage $message): string
    {
        return $message->getEndpointUrl();
    }

    /**
     * Get the request headers
     *
     * @param PushMessage $message
     *
     * @return string[]
     */
    private function getHeaders(PushMessage $message): array
    {
        return [
            'Content-Type' => 'application/json',
            'Content-Length' => $message->getContentLength(),
            'Encryption' => 'salt='.$message->getSalt(),
            'Crypto-Key' => 'dh='.$message->getCryptoKey(),
            'Content-Encoding' => 'aesgcm',
            'TTL' => $message->getTTL(),
        ];
    }

    /**
     * Get the request body
     *
     * @param PushMessage $message
     *
     * @return string
     */
    private function getBody(PushMessage $message): string
    {
        return $message->getBody();
    }
}
