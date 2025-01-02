<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ config('app.name') }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @php
        $menus = \App\Models\Menu::with(['children' => function($query) {
                $query->where('is_active', true)
                    ->orderBy('order');
            }])
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('module')
            ->orderBy('order')
            ->get()
            ->groupBy('module');
    @endphp

    @foreach ($menus as $module => $moduleMenus)
        @php
            $hasPermission = false;
            foreach ($moduleMenus as $menu) {
                if (!$menu->permission_name || auth()->user()->can($menu->permission_name)) {
                    $hasPermission = true;
                    break;
                }
            }
        @endphp

        @if ($hasPermission)
            <!-- Heading -->
            <div class="sidebar-heading">
                {{ ucfirst($module) }}
            </div>

            @foreach ($moduleMenus as $menu)
                @if (!$menu->permission_name || auth()->user()->can($menu->permission_name))
                    @if ($menu->route)
                        {{-- Single Menu dengan Route --}}
                        <li class="nav-item {{ request()->routeIs($menu->route.'*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route($menu->route) }}">
                                <i class="{{ $menu->icon }}"></i>
                                <span>{{ $menu->name }}</span>
                            </a>
                        </li>
                    @else
                        {{-- Menu tanpa route menjadi parent/dropdown --}}
                        @php
                            $isChildActive = $menu->children->contains(function($child) {
                                return request()->routeIs($child->route.'*');
                            });
                        @endphp

                        <li class="nav-item {{ $isChildActive ? 'active' : '' }}">
                            <a class="nav-link {{ !$isChildActive ? 'collapsed' : '' }}"
                                href="#"
                                data-toggle="collapse"
                                data-target="#collapse{{ Str::slug($menu->name) }}"
                                aria-expanded="{{ $isChildActive ? 'true' : 'false' }}"
                                aria-controls="collapse{{ Str::slug($menu->name) }}">
                                <i class="{{ $menu->icon }}"></i>
                                <span>{{ $menu->name }}</span>
                            </a>
                            <div id="collapse{{ Str::slug($menu->name) }}"
                                    class="collapse {{ $isChildActive ? 'show' : '' }}"
                                    aria-labelledby="heading{{ Str::slug($menu->name) }}"
                                    data-parent="#accordionSidebar">
                                <div class="bg-white py-2 collapse-inner rounded">
                                    @if($menu->children->isNotEmpty())
                                        @foreach($menu->children as $child)
                                            @if(!$child->permission_name || auth()->user()->can($child->permission_name))
                                                <a class="collapse-item {{ request()->routeIs($child->route.'*') ? 'active' : '' }}"
                                                    href="{{ route($child->route) }}">
                                                    <i class="{{ $child->icon }} fa-fw mr-1"></i>
                                                    {{ $child->name }}
                                                </a>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endif
                @endif
            @endforeach

            <!-- Divider -->
            <hr class="sidebar-divider">
        @endif
    @endforeach

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
