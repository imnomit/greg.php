<?php

namespace nomit\Security\Authorization\User;

use nomit\Database\ConnectionInterface;
use nomit\Database\ExplorerInterface;
use nomit\Dumper\Dumper;
use nomit\Security\Authorization\Permission\Permission;
use nomit\Security\Authorization\Permission\PermissionInterface;
use nomit\Security\Authorization\Role\Role;
use nomit\Security\Authorization\Role\RoleInterface;
use nomit\Security\User\UserInterface;
use Psr\Log\LoggerInterface;

class DatabaseUserAuthorizationProvider implements UserAuthorizationProviderInterface
{

    public function __construct(
        private ConnectionInterface $database,
        private ExplorerInterface $explorer,
        private string $rolesTableName,
        private string $userRolesTableName,
        private string $permissionsTableName,
        private string $rolePermissionsTableName,
        private ?LoggerInterface $logger = null,
    )
    {
    }
    
    public function getPermissions(RoleInterface $role = null): array
    {
        if($role !== null) {
            return $this->getRolePermissions($role);
        }
        
        $permissions = [];
        $permissionRows = $this->explorer
            ->table($this->permissionsTableName)
            ->fetchAll();
        
        foreach($permissionRows as $permissionRow) {
            $permission = new Permission();
            $permission = $permission->fromArray($permissionRow->toArray());
            
            $permissions[$permission->getName()] = $permission;
        }
        
        return $permissions;
    }

    /**
     * @param UserInterface $user
     * @return array
     * @throws \Exception
     */
    public function getRoles(UserInterface $user = null): array
    {
        if(!$user) {
            $roles = [];
            $roleRows = $this->explorer
                ->table($this->rolesTableName)
                ->fetchAll();

            foreach($roleRows as $roleRow) {
                $role = new Role($roleRow->name);
                $role = $role->fromArray($roleRow->toArray());

                $role->setPermissions($this->getRolePermissions($role));

                $roles[$role->getName()] = $role;
            }

            return $roles;
        }

        $userRoles = $this->explorer
            ->table($this->userRolesTableName)
            ->where('user_id', $user->getUserId())
            ->fetchAll();

        $roles = [];

        foreach($userRoles as $userRole) {
            $roleData = $this->explorer
                ->table($this->rolesTableName)
                ->where('id', $userRole->id)
                ->fetch();

            if(!$roleData) {
                continue;
            }

            $role = new Role($roleData->name);
            $role = $role->fromArray($roleData->toArray());

            $role->setPermissions($this->getRolePermissions($role));

            $roles[$role->getName()] = $role;
        }

        return $roles;
    }

    /**
     * @param RoleInterface $role
     * @return array
     * @throws \Exception
     */
    public function getRolePermissions(RoleInterface $role): array
    {
        $rolePermissions = $this->explorer
            ->table($this->rolePermissionsTableName)
            ->where('role_id', $role->getRoleId())
            ->fetchAll();

        $permissions = [];

        foreach($rolePermissions as $rolePermission) {
            $permissionData = $rolePermission->related($this->permissionsTableName, 'id')
                ->fetch();

            if(!$permissionData) {
                continue;
            }

            $permission = new Permission();
            $permission = $permission->fromArray($permissionData->toArray());

            $permissions[$permission->getName()] = $permission;
        }

        return $permissions;
    }

    public function saveRole(RoleInterface $role): int|bool
    {
        $roleData = $role->toArray();

        unset($roleData['role_id']);
        unset($roleData['permissions']);

        try {
            if($this->explorer
                ->table($this->rolesTableName)
                ->where('id', $role->getRoleId())
                ->count() > 0
            ) {
                return $this->database
                    ->query('UPDATE ?name SET ?', $this->rolesTableName, $roleData, 'WHERE ?', [
                        'id' => $role->getRoleId()
                    ])
                    ->getRowCount() > 0;
            }

            if($this->database
                ->query('INSERT INTO ?name ?', $this->rolesTableName, $roleData)
                ->getRowCount() > 0
            ) {
                return $this->database->getInsertId();
            }

            return false;
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to save to the database a role with the role name {roleName}: {message}.', [
                'roleName' => $role->getName(),
                'role' => $role->toArray(),
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }
    }

    public function savePermission(PermissionInterface $permission): int|bool
    {
        $permissionData = $permission->toArray();

        unset($permissionData['permission_id']);

        try {
            if($this->explorer
                ->table($this->permissionsTableName)
                ->where('id', $permission->getPermissionId())
                ->count() > 0
            ) {
                return $this->database
                    ->query('UPDATE ?name SET ?', $this->permissionsTableName, $permissionData, 'WHERE ?', [
                        'id' => $permission->getPermissionId()
                    ])
                    ->getRowCount() > 0;
            }

            if($this->database
                ->query('INSERT INTO ?name ?', $this->permissionsTableName, $permission)
                ->getRowCount() > 0
            ) {
                return $this->database->getInsertId();
            }

            return false;
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to save to the database a permission with the permission name {permissionName}: {message}.', [
                'permissionName' => $permission->getName(),
                'permission' => $permission->toArray(),
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }
    }

    public function getRole(string $roleName): ?RoleInterface
    {
        try {
            $roleData = $this->explorer
                ->table($this->rolesTableName)
                ->where('name', $roleName)
                ->fetch();

            if(!$roleData) {
                return null;
            }

            $role = new Role($roleData->name);
            $role = $role->fromArray($roleData->toArray());

            $permissions = [];
            $permissionRows = $roleData
                ->related($this->rolePermissionsTableName)
                ->fetchAll();

            foreach($permissionRows as $permission) {
                $permission = $this->getPermissionById($permission->permission_id);

                if(!$permission) {
                    continue;
                }

                $permissions[] = $permission;
            }

            $role->setPermissions($permissions);

            return $role;
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to fetch from the database the data for the role with the role name {roleName}: {message}.', [
                'roleName' => $roleName,
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return null;
        }
    }

    public function getPermission(string $permissionName): ?PermissionInterface
    {
        try {
            $permissionData = $this->explorer
                ->table($this->permissionsTableName)
                ->where('name', $permissionName)
                ->fetch();

            if(!$permissionData) {
                return null;
            }

            $permission = new Permission();

            return $permission->fromArray($permissionData->toArray());
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to fetch from the database the data for the permission with the permission name {permissionName}: {message}.', [
                'permissionName' => $permissionName,
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return null;
        }
    }

    private function getPermissionById(int $permissionId): ?PermissionInterface
    {
        try {
            $permissionData = $this->explorer
                ->table($this->permissionsTableName)
                ->where('id', $permissionId)
                ->fetch();

            if(!$permissionData) {
                return null;
            }

            $permission = new Permission();

            return $permission->fromArray($permissionData->toArray());
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to fetch from the database the data for the permission with the permission ID {permissionId}: {message}.', [
                'permissionId' => $permissionId,
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return null;
        }
    }

    public function addPermissionToRole(RoleInterface $role, PermissionInterface $permission): int|bool
    {
        try {
            if($this->explorer
                    ->table($this->rolePermissionsTableName)
                    ->where('role_id', $role->getRoleId())
                    ->where('permission_id', $permission->getPermissionId())
                    ->count() > 0
            ) {
                return true;
            }

            return $this->database
                ->transaction(function(ConnectionInterface $database) use($role, $permission) {
                    if($database
                            ->query('INSERT INTO ?name ?', $this->rolePermissionsTableName, [
                                'role_id' => $role->getRoleId(),
                                'permission_id' => $permission->getPermissionId(),
                                'datetime' => new \DateTime('NOW')
                            ])
                            ->getRowCount() > 0
                    ) {
                        return $database->getInsertId();
                    }

                    return false;
                });
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to associate in the database the role and permission with the role and permission ID {roleId} and {permissionId}, respectively: {message}.', [
                'roleId' => $role->getRoleId(),
                'permissionId' => $permission->getPermissionId(),
                'role' => $role->toArray(),
                'permission' => $permission->toArray(),
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }
    }

    public function addRoleToUser(RoleInterface $role, UserInterface $user): int|bool
    {
        try {
            if($this->explorer
                    ->table($this->userRolesTableName)
                    ->where('role_id', $role->getRoleId())
                    ->where('user_id', $user->getUserId())
                    ->count() > 0
            ) {
                return true;
            }

            return $this->database
                ->transaction(function(ConnectionInterface $database) use($role, $user) {
                    if($database
                            ->query('INSERT INTO ?name ?', $this->userRolesTableName, [
                                'role_id' => $role->getRoleId(),
                                'user_id' => $user->getUserId()
                            ])
                            ->getRowCount() > 0
                    ) {
                        return $database->getInsertId();
                    }

                    return false;
                });
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to associate in the database the user and role with the user and role ID {userId} and {roleId}, respectively: {message}.', [
                'userId' => $user->getUserId(),
                'roleId' => $role->getRoleId(),
                'user' => $user->toArray(),
                'role' => $role->toArray(),
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }
    }

    public function removePermissionFromRole(RoleInterface $role, PermissionInterface $permission): bool
    {
        try {
            if($this->explorer
                    ->table($this->rolePermissionsTableName)
                    ->where('role_id', $role->getRoleId())
                    ->where('permission_id', $permission->getPermissionId())
                    ->count() < 1
            ) {
                return true;
            }

            return $this->database
                ->transaction(function(ConnectionInterface $database) use($role, $permission) {
                    return $database
                            ->query('DELETE FROM ?name WHERE ?', $this->rolePermissionsTableName, [
                                'role_id' => $role->getRoleId(),
                                'permission_id' => $permission->getPermissionId()
                            ])
                            ->getRowCount() > 0;
                });
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to disassociate in the database the role and permission with the role and permission ID {roleId} and {permissionId}, respectively: {message}.', [
                'roleId' => $role->getRoleId(),
                'permissionId' => $permission->getPermissionId(),
                'role' => $role->toArray(),
                'permission' => $permission->toArray(),
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }
    }

    public function removeRoleFromUser(RoleInterface $role, UserInterface $user): bool
    {
        try {
            if($this->explorer
                    ->table($this->userRolesTableName)
                    ->where('role_id', $role->getRoleId())
                    ->where('user_id', $user->getUserId())
                    ->count() < 1
            ) {
                return true;
            }

            return $this->database
                ->transaction(function(ConnectionInterface $database) use($role, $user) {
                    return $database
                            ->query('DELETE FROM ?name WHERE ?', $this->userRolesTableName, [
                                'role_id' => $role->getRoleId(),
                                'user_id' => $user->getUserId()
                            ])
                            ->getRowCount() > 0;
                });
        } catch(\Throwable $exception) {
            $this->logger?->error('An error occurred while attempting to disassociate in the database the user and role with the user and role ID {userId} and {roleId}, respectively: {message}.', [
                'userId' => $user->getUserId(),
                'roleId' => $role->getRoleId(),
                'user' => $user->toArray(),
                'role' => $role->toArray(),
                'message' => $exception->getMessage(),
                'exception' => $exception
            ]);

            return false;
        }
    }

}