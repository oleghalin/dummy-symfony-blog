<?php

namespace App\Security\Firewall;

use Firebase\JWT\JWT;
use FOS\OAuthServerBundle\Security\Firewall\OAuthListener as BaseOAuthListener;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OAuthListener extends BaseOAuthListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(
        $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        \OAuth2\OAuth2 $serverService,
        ContainerInterface $container
    ) {
        parent::__construct($securityContext, $authenticationManager, $serverService);
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event The event.
     * @throws \OAuth2\OAuth2AuthenticateException
     */
    public function handle(GetResponseEvent $event)
    {
        if ($event->getRequest()->cookies->has('symfony_cookie')) {
            $token = $this->authenticateViaCookie($event);
        } elseif (null !== $oAuthToken = $this->authenticateViaBearer($event)) {
            $token = $oAuthToken;
        } else {
            return;
        }

        if (null === $token) {
            return;
        }

        try {
            $returnValue = $this->authenticationManager->authenticate($token);

            if ($returnValue instanceof TokenInterface) {
                return $this->securityContext->setToken($returnValue);
            }

            if ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {
            if (null !== $p = $e->getPrevious()) {
                $event->setResponse($p->getHttpResponse());
            }
        }
    }

    /**
     * @param GetResponseEvent $event
     * @return \FOS\OAuthServerBundle\Security\Authentication\Token\OAuthToken
     * @throws \OAuth2\OAuth2AuthenticateException
     */
    protected function authenticateViaBearer($event)
    {
        $oauthToken = $this->serverService->getBearerToken($event->getRequest(), true);

        $token = new OAuthToken();
        $token->setToken($oauthToken);

        return $token;
    }

    /**
     * Authenticate the incoming request via the token cookie.
     *
     * @param  GetResponseEvent $event
     * @return mixed
     */
    protected function authenticateViaCookie($event)
    {
        $request = $event->getRequest();
        // If we need to retrieve the token from the cookie, it'll be encrypted so we must
        // first decrypt the cookie and then attempt to find the token value within the
        // database. If we can't decrypt the value we'll bail out with a null return.
        try {
            $token = $this->decodeJwtTokenCookie($request);
        } catch (\Exception $e) {
            return;
        }

        // We will compare the CSRF token in the decoded API token against the CSRF header
        // sent with the request. If the two don't match then this request is sent from
        // a valid source and we won't authenticate the request for further handling.
        if (! $this->validCsrf($token, $request) ||
            time() >= $token['expiry']) {
            return;
        }

        $user = $this->container->get('doctrine')->getRepository('App:User')->find($token['sub']);
        if (! $user) {
            return;
        }

        return new UsernamePasswordToken($user, null, 'main', $user->getRoles());
    }

    /**
     * Decode and decrypt the JWT token cookie.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    protected function decodeJwtTokenCookie($request)
    {
        return (array) JWT::decode(
            $request->cookies->get('symfony_cookie'),
            $this->container->getParameter('kernel.secret'), ['HS256']
        );
    }

    /**
     * Determine if the CSRF / header are valid and match.
     *
     * @param  array $token
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return bool
     */
    protected function validCsrf($token, $request)
    {
        return isset($token['csrf']) && hash_equals(
                $token['csrf'], (string) $request->headers->get('X-CSRF-TOKEN')
            );
    }
}