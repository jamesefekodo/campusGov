<?php

namespace App\Models\Scopes;

use App\Exceptions\TenantContextMissingException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    // public function apply(Builder $builder, Model $model): void
    // {
    //     if (tenantId()) {
    //         $builder->where(
    //             $model->getTable().'.tenant_id',
    //             tenantId()
    //         );
    //     }
    // }
    public function apply(Builder $builder, Model $model): void
    {
        // Keep your existing console behavior.
        if (app()->runningInConsole()) {
            return;
        }

        $tenantId = tenantId();

        if (!$tenantId) {
            throw new TenantContextMissingException(
                'Tenant context is missing for [' .
                    $model->getTable() . '].'
            );
        }

        $builder->where(
            $model->qualifyColumn('tenant_id'),
            $tenantId
        );
    }
}
