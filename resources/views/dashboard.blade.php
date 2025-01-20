@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    {{-- <div class="container"> --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>Dashboard</h1>
                    @if ($roles->count() > 1)
                        <div class="btn-group" role="group">
                            @foreach ($roles as $role)
                                <button type="button"
                                    class="btn btn-outline-primary role-tab {{ $loop->first ? 'active' : '' }}"
                                    data-role="{{ $role->name }}">
                                    @switch($role->name)
                                        @case('direktur')
                                            <i class="fas fa-user-tie me-2"></i>
                                        @break

                                        @case('keuangan')
                                            <i class="fas fa-money-bill me-2"></i>
                                        @break

                                        @case('admin')
                                            <i class="fas fa-tools me-2"></i>
                                        @break

                                        @case('teknisi')
                                            <i class="fas fa-wrench me-2"></i>
                                        @break

                                        @case('unit')
                                            <i class="fas fa-building me-2"></i>
                                        @break

                                        @case('superadmin')
                                            <i class="fas fa- me-2"></i>
                                        @break

                                        @default
                                            <i class="fas fa-user me-2"></i>
                                    @endswitch
                                    {{ ucfirst($role->name) }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @foreach ($roles as $role)
            <div class="role-content" id="role-{{ $role->name }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                @if (view()->exists("dashboard.{$role->name}"))
                    @include("dashboard.{$role->name}", ['data' => $dashboardData[$role->name]])
                @else
                    <div class="alert alert-warning">
                        Dashboard untuk role {{ $role->name }} belum tersedia.
                    </div>
                @endif
            </div>
        @endforeach
    {{-- </div> --}}
@endsection

@if ($roles->count() > 1)
    @push('styles')
        <style>
            .btn-group .btn-outline-primary.active {
                background-color: #007bff;
                color: white;
            }

            .role-content {
                transition: all 0.3s ease;
            }

            .btn-group .btn-outline-primary:hover {
                background-color: #e9ecef;
                color: #007bff;
            }

            .btn-group .btn-outline-primary.active:hover {
                background-color: #0056b3;
                color: white;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle tab switching
                document.querySelectorAll('.role-tab').forEach(button => {
                    button.addEventListener('click', function() {
                        // Hide all role contents with fade effect
                        document.querySelectorAll('.role-content').forEach(content => {
                            content.style.opacity = '0';
                            setTimeout(() => {
                                content.style.display = 'none';
                            }, 300);
                        });

                        // Remove active class from all tabs
                        document.querySelectorAll('.role-tab').forEach(tab => {
                            tab.classList.remove('active');
                        });

                        // Show selected role content and activate tab
                        const roleContent = document.querySelector(`#role-${this.dataset.role}`);
                        if (roleContent) {
                            setTimeout(() => {
                                roleContent.style.display = 'block';
                                setTimeout(() => {
                                    roleContent.style.opacity = '1';
                                }, 50);
                            }, 300);
                            this.classList.add('active');
                        }

                        // Save active tab to localStorage
                        localStorage.setItem('activeRole', this.dataset.role);
                    });
                });

                // Restore active tab from localStorage
                const savedRole = localStorage.getItem('activeRole');
                if (savedRole) {
                    const savedTab = document.querySelector(`.role-tab[data-role="${savedRole}"]`);
                    if (savedTab) {
                        savedTab.click();
                    }
                }
            });
        </script>
    @endpush
@endif
