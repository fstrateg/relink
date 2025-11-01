<!doctype html>
<html lang="en">
<!--begin::Head-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>LaLetty Чат боты</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
            rel="stylesheet"
            href="/assets/overlayscrollbars.min.css"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
            rel="stylesheet"
            href="/assets/bootstrap-icons.min.css"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="/adminlte/dist/css/adminlte.css" />
</head>
<!--end::Head-->
<!--begin::Body-->
<body class="layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary">
<!--begin::App Wrapper-->
<div class="app-wrapper">
    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Start Navbar Links-->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <!--li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
                <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li-->
            </ul>
            <!--end::Start Navbar Links-->
            <!--begin::End Navbar Links-->
            <ul class="navbar-nav ms-auto">
                <!--begin::Navbar Search-->
                <li class="nav-item">
                    <a class="nav-link" data-widget="navbar-search" href="<?= site_url('auth/logout') ?>" role="button">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </li>
                <!--end::Navbar Search-->
            </ul>
            <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
    </nav>
    <!--end::Header-->
    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
            <a href="/admin" class="brand-link">
                <img
                        src="/adminlte/dist/assets/img/AdminLTELogo.png"
                        alt="Чат боты"
                        class="brand-image opacity-75 shadow"
                />
                <span class="brand-text fw-light">Чат боты</span>
            </a>
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <!--begin::Sidebar Menu-->
                <ul
                        class="nav sidebar-menu flex-column"
                        data-lte-toggle="treeview"
                        role="menu"
                        data-accordion="false"
                >
                    <li class="nav-item">
                        <a href="/admin" class="nav-link">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/admin/chatlog" class="nav-link">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>
                                Лог запросов
                            </p>
                        </a>
                    </li>
                </ul>
                <!--end::Sidebar Menu-->
            </nav>
        </div>
        <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <main class="app-main">
        <!--begin::App Content-->
        <div class="app-content">
            <div class="container-fluid">
            <?= $this->renderSection('content') ?>
            </div>
        </div>
        <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">LaLetty Bot</div>
        <strong>
            Copyright &copy; 2014-2024&nbsp;
        </strong>
        All rights reserved.
    </footer>
    <!--end::Footer-->
</div>
<!--end::App Wrapper-->
<!--begin::Third Party Plugin(OverlayScrollbars)-->
<script
        src="/assets/overlayscrollbars.browser.es6.min.js"
></script>
<!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
<script
        src="/assets/popper.min.js"
></script>
<!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
<script
        src="/assets/bootstrap/js/bootstrap.min.js"
></script>
<!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
<script src="/adminlte/dist/js/adminlte.js"></script>
<!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
<script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                scrollbars: {
                    theme: Default.scrollbarTheme,
                    autoHide: Default.scrollbarAutoHide,
                    clickScroll: Default.scrollbarClickScroll,
                },
            });
        }
    });
</script>
<!--end::OverlayScrollbars Configure-->
</body>
</html>
