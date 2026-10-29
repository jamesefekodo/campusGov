<?php

namespace App\Models\Traits;

use App\Exceptions\TenantContextMissingException;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    // protected static function bootBelongsToTenant(): void
    // {
    //     static::addGlobalScope(new TenantScope());

    //     // static::creating(function (Model $model): void {

    //     //     if (tenantId() && empty($model->tenant_id)) {
    //     //         $model->tenant_id = tenantId();
    //     //     }

    //     // });
    //     static::creating(function (Model $model): void {
    //         if (empty($model->tenant_id)) {
    //             $tenantId = tenantId();

    //             if (!$tenantId && !app()->runningInConsole()) {
    //                 throw new \App\Exceptions\TenantContextMissingException(
    //                     'Tenant context is missing while creating [' . $model->getTable() . '].'
    //                 );
    //             }

    //             $model->tenant_id = $tenantId;
    //         }
    //     });
    // }

    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Model $model): void {
            if (!empty($model->tenant_id)) {
                return;
            }

            $tenantId = tenantId();

            if (!$tenantId) {
                throw new TenantContextMissingException(
                    'Tenant context is missing while creating [' .
                    $model->getTable() . '].'
                );
            }

            $model->tenant_id = $tenantId;
        });
    }
}
