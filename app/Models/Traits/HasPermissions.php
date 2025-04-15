<?php

namespace App\Models\Traits;

use App\Models\Permission;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'model_has_permissions',
            'model_id',
            'permission_id'
        )->wherePivot('model_type', static::getMorphClass());
    }


    public function can($permission): bool
    {
        return $this->permissions()->get()->pluck('name')->contains($permission);
    }

    public function givePermissionTo($permissions): self
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $permissionIds = array_map([$this, 'resolvePermissionId'], $permissions);

        foreach ($permissionIds as $permissionId) {
            DB::table('model_has_permissions')->updateOrInsert([
                'permission_id' => $permissionId,
                'model_type' => static::getMorphClass(),
                'model_id'    => $this->{$this->getKeyName()},
            ]);
        }

        return $this;
    }

    public function revokePermissionTo($permissions): self
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $permissionIds = array_map([$this, 'resolvePermissionId'], $permissions);

        DB::table('model_has_permissions')
            ->where('model_type', static::getMorphClass())
            ->where('model_id', $this->{$this->getKeyName()})
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        return $this;
    }

    public function syncPermissions($permissions): self
    {
        $permissionIds = array_map([$this, 'resolvePermissionId'], is_array($permissions) ? $permissions : [$permissions]);

        DB::table('model_has_permissions')
            ->where('model_type', static::getMorphClass())
            ->where('model_id', $this->{$this->getKeyName()})
            ->delete();

        foreach ($permissionIds as $permissionId) {
            DB::table('model_has_permissions')->insert([
                'permission_id' => $permissionId,
                'model_type' => static::getMorphClass(),
                'model_id' => $this->{$this->getKeyName()},
            ]);
        }

        return $this;
    }

    public function hasPermissionTo($permission): bool
    {
        $permissionId = $this->resolvePermissionId($permission);

        return DB::table('model_has_permissions')
            ->where('model_type', static::getMorphClass())
            ->where('model_id', $this->{$this->getKeyName()})
            ->where('permission_id', $permissionId)
            ->exists();
    }

    protected function resolvePermissionId($permission)
    {
        if ($permission instanceof Permission) {
            return $permission->id;
        }

        if (is_numeric($permission)) {
            return (int) $permission;
        }

        return Permission::where('name', $permission)->value('id');
    }
}
