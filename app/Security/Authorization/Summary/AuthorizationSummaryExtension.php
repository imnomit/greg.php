<?php

namespace Application\Security\Authorization\Summary;

use Application\Summary\Administration\AbstractSummaryExtension;
use nomit\Security\Authorization\AuthorizationManagerInterface;
use nomit\Security\Authorization\Permission\PermissionInterface;
use nomit\Security\Authorization\Role\RoleInterface;

final class AuthorizationSummaryExtension extends AbstractSummaryExtension
{

    public function __construct(
        private AuthorizationManagerInterface $authorizationManager
    )
    {
        parent::__construct();
    }

    public function getName(): string
    {
        return 'security.authorization';
    }

    public function summarize(): void
    {
        $roles = $this->authorizationManager->getRoles();
        $permissions = $this->authorizationManager->getPermissions();

        $this->set('roles', array_map(function(RoleInterface $role) {
            return $role->toArray();
        }, $roles));

        $this->set('permissions', array_map(function(PermissionInterface $permission) {
            return $permission->toArray();
        }, $permissions));
    }

}