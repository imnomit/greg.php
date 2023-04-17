<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\Security\Authorization\Voter;

use nomit\Dumper\Dumper;
use nomit\Security\Authentication\Token\TokenInterface;
use nomit\Security\Authorization\Role\Role;
use nomit\Security\Authorization\Role\RoleInterface;
use function nomit\dump;

/**
 * RoleVoter votes if any attribute starts with a given prefix.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoleVoter implements VoterInterface
{

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        $result = VoterInterface::ACCESS_ABSTAIN;
        $roles = $this->extractRoles($token);

        foreach ($attributes as $attribute) {
            if ($attribute instanceof Role) {
                $attribute = $attribute->getName();
            }

            if (!\is_string($attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_DENIED;

            foreach ($roles as $role) {
                if ($attribute === strtoupper($role)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return $result;
    }

    protected function extractRoles(TokenInterface $token)
    {
        if (method_exists($token, 'getRoleNames')) {
            return $token->getRoleNames();
        }

        return array_map(function (RoleInterface $role) {
            return $role->getName();
        }, $token->getRoles(false));
    }
}
