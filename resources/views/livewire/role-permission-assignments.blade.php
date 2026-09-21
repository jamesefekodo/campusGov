<div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Permissions
                    </h5>
                    <small class="text-muted">Manage access for <strong>{{ $role->name }}</strong></small>
                </div>

                <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 fs-6">
                    {{ count($assignedPermissionIds) }} assigned
                </div>
            </div>

            <div class="mt-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        class="form-control border-start-0"
                        placeholder="Search permissions or modules...">
                </div>
            </div>
        </div>

        <div class="card-body pt-2">
            @forelse ($permissionsByModule as $module => $permissions)
                @php
                    $moduleIds = $permissions->pluck('id')->toArray();
                    $assignedInModule = count(array_intersect($moduleIds, $assignedPermissionIds));
                    $allAssigned = $assignedInModule === count($moduleIds);
                @endphp

                <div class="mb-4 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-folder-open text-secondary"></i>
                            <span class="fw-semibold text-uppercase small text-muted" style="letter-spacing: 0.5px;">
                                {{ $module ?: 'Uncategorized' }}
                            </span>
                            <span class="badge bg-light text-dark border">
                                {{ $assignedInModule }}/{{ count($moduleIds) }}
                            </span>
                        </div>

                        <button
                            type="button"
                            wire:click="toggleModule('{{ $module }}', {{ $allAssigned ? 'false' : 'true' }})"
                            class="btn btn-sm {{ $allAssigned ? 'btn-outline-danger' : 'btn-outline-primary' }}">
                            {{ $allAssigned ? 'Deselect all' : 'Select all' }}
                        </button>
                    </div>

                    <div class="row g-2">
                        @foreach ($permissions as $permission)
                            @php $isAssigned = in_array($permission->id, $assignedPermissionIds); @endphp

                            <div class="col-md-3">
                                <div
                                    wire:click="togglePermission({{ $permission->id }})"
                                    wire:loading.class="opacity-50"
                                    wire:target="togglePermission({{ $permission->id }})"
                                    class="d-flex align-items-center justify-content-between p-2 px-3 rounded-3 border cursor-pointer {{ $isAssigned ? 'border-primary bg-primary bg-opacity-10' : 'border-light-subtle bg-light' }}"
                                    style="cursor: pointer; transition: all 0.15s ease;">

                                    <span class="small {{ $isAssigned ? 'fw-semibold text-primary' : 'text-dark' }}">
                                        {{ $permission->name }}
                                    </span>

                                    <div class="form-check form-switch mb-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            style="pointer-events: none;"
                                            {{ $isAssigned ? 'checked' : '' }}
                                            readonly>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0">No permissions match your search.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>