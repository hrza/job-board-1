<?php

namespace spec\AppBundle\Security;

use AppBundle\Entity\Token;
use AppBundle\Service\Token\TokenRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('AppBundle\Security\TokenProvider');
    }

    function let(TokenRepository $tokenRepository)
    {
        $this->beConstructedWith($tokenRepository);
    }

    function it_is_user_provider()
    {
        $this->shouldImplement(UserProviderInterface::class);
    }

    function it_supports_token_class()
    {
        $this->supportsClass(Token::class)->shouldReturn(true);
    }

    function it_loads_token(Token $token, $tokenRepository)
    {
        $tokenId = '123';

        $tokenRepository->findById($tokenId)->willReturn($token);

        $this->loadUserByUsername($tokenId)->shouldReturn($token);
    }

    function it_throws_exception_on_invalid_id($tokenRepository)
    {
        $tokenId = '123';

        $tokenRepository->findById($tokenId)->willReturn(null);

        $this->shouldThrow(UsernameNotFoundException::class)->during('loadUserByUsername', [$tokenId]);
    }
}
