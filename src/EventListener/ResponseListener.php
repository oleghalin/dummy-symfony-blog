<?php

namespace App\EventListener;

use App\Factory\ApiTokenCookieFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Http\FirewallMapInterface;

class ResponseListener
{
    protected $container;

    /**
     * @var \Symfony\Component\Security\Http\FirewallMapInterface
     */
    private $firewallMap;

    private $token;

    /**
     * @var \App\Factory\ApiTokenCookieFactory
     */
    private $cookieFactory;

    /**
     * ResponseListener constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Symfony\Component\Security\Http\FirewallMapInterface $firewallMap
     * @param \App\Factory\ApiTokenCookieFactory $cookieFactory
     */
    public function __construct(
        ContainerInterface $container,
        FirewallMapInterface $firewallMap,
        ApiTokenCookieFactory $cookieFactory)
    {
        $this->container = $container;
        $this->firewallMap = $firewallMap;
        $this->token = $this->container->get('security.token_storage')->getToken();
        $this->cookieFactory = $cookieFactory;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        $firewallName = $this->firewallMap->getFirewallConfig($request)->getName();

        if ($firewallName === 'main') {
            $response = $event->getResponse();
            $tokenProvider = $this->container->get('security.csrf.token_manager');
            $token = $tokenProvider->getToken('auth')->getValue();

            if ($this->shouldReceiveFreshToken($request, $response)) {
                $response->headers->setCookie(
                    $this->cookieFactory->make($this->token->getUser()->getId(), $token)
                );
            }
        }
    }

    private function shouldReceiveFreshToken(Request $request, Response $response)
    {
        return $this->requestShouldReceiveFreshToken($request) &&
            $this->responseShouldReceiveFreshToken($response);
    }

    private function requestShouldReceiveFreshToken(Request $request)
    {
        return $this->token && $request->isMethod('GET') && $this->token->getUser() !== 'anon.';
    }

    private function responseShouldReceiveFreshToken(Response $response)
    {
        return ($response instanceof Response ||
                $response instanceof JsonResponse) &&
            ! $this->alreadyContainsToken($response);
    }

    /**
     * Determine if the given response already contains an API token.
     *
     * This avoids us overwriting a just "refreshed" token.
     *
     * @param  Response  $response
     * @return bool
     */
    protected function alreadyContainsToken($response)
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'symfony_token') {
                return true;
            }
        }

        return false;
    }
}