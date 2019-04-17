<?php

namespace App;


use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class SimpleAuthenticationListener implements ListenerInterface
{
    private $tokenStorage;

    private $authenticationManager;

    private $providerKey;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager, string $providerKey) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->providerKey = $providerKey;
    }


    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->query->has('username') && $request->query->has('password')) {
            $username = $request->query->get('username');
            $password = $request->query->get('password');

            $unauthenticatedToken = new UsernamePasswordToken(
                $username,
                $password,
                $this->providerKey
            );

            try {
                $authenticatedToken = $this
                    ->authenticationManager
                    ->authenticate($unauthenticatedToken);

                $this->tokenStorage->setToken($authenticatedToken);
            } catch (AuthenticationException $exception) {
                $response = new Response($exception->getMessage());

                $event->setResponse($response);
            }

        }
    }
}