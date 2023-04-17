<?php

namespace nomit\Security\Authorization\User;

use nomit\Security\Authorization\Permission\PermissionInterface;
use nomit\Security\Authorization\Role\RoleInterface;
use nomit\Security\User\UserInterface;

interface UserAuthorizationProviderInterface
{

    public function getRoles(UserInterface $user = null): array;
    
    public function getPermissions(RoleInterface $role = null): array;

    public function getRolePermissions(RoleInterface $role): array;

    public function saveRole(RoleInterface $role): int|bool;

    public function savePermission(PermissionInterface $permission): int|bool;

    public function getRole(string $roleName): ?RoleInterface;

    public function getPermission(string $permissionName): ?PermissionInterface;

    public function addPermissionToRole(RoleInterface $role, PermissionInterface $permission):int| bool;

    public function addRoleToUser(RoleInterface $role, UserInterface $user): int|bool;

    public function removePermissionFromRole(RoleInterface $role, PermissionInterface $permission): bool;

    public function removeRoleFromUser(RoleInterface $role, UserInterface $user): bool;

}