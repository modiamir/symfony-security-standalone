<?php

namespace App;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;

class SimpleAuthenticationProvider implements AuthenticationProviderInterface
{
    public function authenticate(TokenInterface $token)
    {
        $username = $token->getUsername();
        $password = $token->getCredentials();

        if ($username == 'amir' && $password == 'foo') {
            $user = new User($username, $password, ['ROLE_USER']);
            $authenticatedToken = new UsernamePasswordToken($user, $password, $token->getProviderKey(), $user->getRoles());

            return $authenticatedToken;
        }

        throw new AuthenticationException("Username or password is invalid.");
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken && !$token->isAuthenticated();
    }
}