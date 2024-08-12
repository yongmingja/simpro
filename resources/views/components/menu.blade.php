<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <div class="layout-page">

            <div class="content-wrapper">
                @include('rsc.navbar')

                @yield('breadcrumbs')
                @yield('content')

                @include('rsc.sidebar')
                
                @include('rsc.footer')

            </div>

        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
</div>