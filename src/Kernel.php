<?php

namespace App;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\FirewallMap;

class Kernel implements HttpKernelInterface
{
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $tokenStorage = new TokenStorage();
        $authenticationManager = new AuthenticationProviderManager([new SimpleAuthenticationProvider()]);
        $simpleAuthenticationListener = new SimpleAuthenticationListener($tokenStorage, $authenticationManager, 'main');

        $firewallMap = new FirewallMap();

        $requestMatcher = new RequestMatcher('^/');
        $listeners = [$simpleAuthenticationListener];
        $firewallMap->add($requestMatcher, $listeners);

        $eventDispatcher = new EventDispatcher();
        $firewall = new Firewall($firewallMap, $eventDispatcher);

        $eventDispatcher->addSubscriber($firewall);

        $getResponseEvent = new GetResponseEvent($this, $request, $type);
        $eventDispatcher->dispatch(KernelEvents::REQUEST, $getResponseEvent);

        if ($getResponseEvent->hasResponse()) {
            return $getResponseEvent->getResponse();
        }

        if (($token = $tokenStorage->getToken()) && $token->getUser()) {
            $user = $token->getUsername();
        } else {
            $user = 'anonymous';
        }

        return new Response(sprintf('Greeting %s', $user));
    }
}
