<?php
namespace Barany\Plaid\MainBundle\Security;

use Barany\Plaid\MainBundle\Entity\ApiToken;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class ApiKeyUserProvider implements UserProviderInterface
{
    /**
     * @var array
     */
    private static $ROLES = [
        'ROLE_API',
    ];

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $apiKey
     * @return User|null
     */
    public function loadUserByUsername($apiKey)
    {
        $apiToken = $this
            ->entityManager
            ->getRepository('Barany\Plaid\MainBundle\Entity\ApiToken')
            ->findOneBy(['token' => $apiKey]);

        /** @var $apiToken ApiToken */
        if (!$apiToken || null == $apiToken->getUser()) {
            return null;
        }
        return $apiToken->getUser();
    }

    /**
     * @param UserInterface $user
     * @return void
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        /**
         * this is used for storing authentication in the session
         * but in this example, the token is sent in each request,
         * so authentication can be stateless. Throwing this exception
         * is proper to make things stateless
         */
        throw new UnsupportedUserException();
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Symfony\Component\Security\Core\User\User' === $class;
    }
}
