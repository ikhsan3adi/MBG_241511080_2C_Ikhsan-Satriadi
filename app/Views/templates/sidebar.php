<?php

// check current path to set active class
/** @var \CodeIgniter\HTTP\URI $uri */
$uri = service('uri');
$currentPath = $uri->getPath();

// remove index.php from the path if exists
$currentPath = str_replace('/index.php/', '', $currentPath);
$currentPath = str_replace('index.php/', '', $currentPath);

helper('text');

$user = currentUser();

?>

<style id="sidebar-styles">
    :root {
        --sidebar-width: 280px;
    }

    .sidebar {
        width: var(--sidebar-width);
        transform: none;
        transition: transform 0.3s ease;
    }

    .sidebar-overlay {
        display: none;
        pointer-events: none;
    }

    .app-shell {
        position: relative;
    }

    @media (max-width: 767.98px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            transform: translateX(-100%);
            z-index: 1050;
        }

        body.sidebar-open .sidebar {
            transform: translateX(0);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        body.sidebar-open {
            overflow: hidden;
        }

        .main-content {
            width: 100%;
            min-height: 100vh;
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
            z-index: 1040;
            display: block;
        }

        body.sidebar-open .sidebar-overlay {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        body.sidebar-open .main-content {
            pointer-events: none;
        }
    }

    @media (min-width: 768px) {
        .sidebar-overlay {
            display: none !important;
        }
    }
</style>

<div id="sidebar" class="sidebar d-flex flex-column flex-shrink-0 p-3 bg-body-secondary">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
        <i class="bi bi-fork-knife pe-none me-3" style="font-size: 24pt;"></i>
        <span class="fs-4">
            <?= env('app.name.short') ?>
        </span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <?php if ($user['role'] == 'gudang'): ?>
            <li>
                <a href="<?= base_url('admin/bahanbaku') ?>" class="nav-link <?= $currentPath === 'admin' || $currentPath === 'admin/bahanbaku' ? 'active' : 'link-body-emphasis' ?>">
                    <i class="bi bi-egg-fill pe-none me-2"></i>
                    Bahan Baku
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/permintaan') ?>" class="nav-link <?= $currentPath === 'admin/permintaan' ? 'active' : 'link-body-emphasis' ?>">
                    <i class="bi bi-basket2-fill pe-none me-2"></i>
                    Permintaan
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="<?= base_url('user/permintaan') ?>" class="nav-link <?= $currentPath === 'user' || $currentPath === 'user/permintaan' ? 'active' : 'link-body-emphasis' ?>">
                    <i class="bi bi-basket2-fill pe-none me-2"></i>
                    Permintaan
                </a>
            </li>
        <?php endif ?>
        <li>
            <hr>
        </li>
        <li>
            <label for="toggleThemeCheckbox" class="w-100">
                <div class="nav-link link-body-emphasis d-flex justify-content-between">
                    <div class="me-5">
                        <i id="toggleThemeIcon" class="bi bi-sun-fill pe-none me-2"></i> Mode Gelap
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleThemeCheckbox" onclick="toggleTheme()">
                    </div>
                </div>
            </label>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://api.dicebear.com/9.x/identicon/svg?backgroundColor=ffffff&seed=<?= $user['email'] ?>" alt="profile-image" width="32" height="32" class="rounded-circle me-2">
            <span class="d-flex flex-column">
                <strong>
                    <?= ellipsize($user['name'], 22) ?>
                </strong>
                <small class="text-muted">
                    <?= $user['email'] ?>
                </small>
            </span>
        </a>
        <ul class="dropdown-menu text-small shadow">
            <!-- <li><a class="dropdown-item" href="#">Settings</a></li> -->
            <!-- <li><a class="dropdown-item" href="#">Profile</a></li> -->
            <!-- <li>
                <hr class="dropdown-divider">
            </li> -->
            <li>
                <a id="logout-button" class="dropdown-item" href="#">
                    Keluar
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    const LOGOUT_ENDPOINT = '<?= url_to('api/logout') ?>';

    document.addEventListener('DOMContentLoaded', function() {
        const logoutButton = document.getElementById('logout-button');

        logoutButton.addEventListener('click', async function(event) {
            event.preventDefault();
            event.stopPropagation();

            if (!localStorage.getItem('jwt_token')) {
                window.location.href = '<?= url_to('login') ?>';
                return;
            }

            await fetch(LOGOUT_ENDPOINT, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('jwt_token'),
                    },
                    credentials: 'include',
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    if (data.error) return;

                    // clear token from local storage
                    localStorage.removeItem('jwt_token');

                    // Redirect
                    window.location.href = data.redirect_url;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleButton = document.getElementById('toggle-sidebar-button');
        const overlay = document.getElementById('sidebarOverlay');
        const sidebarLinks = document.querySelectorAll('#sidebar a.nav-link');
        const sidebarStyles = document.getElementById('sidebar-styles');

        document.head.appendChild(sidebarStyles);

        const openSidebar = () => {
            document.body.classList.add('sidebar-open');
            toggleButton?.setAttribute('aria-expanded', 'true');
        };

        const closeSidebar = () => {
            document.body.classList.remove('sidebar-open');
            toggleButton?.setAttribute('aria-expanded', 'false');
        };

        toggleButton?.addEventListener('click', () => {
            if (document.body.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay?.addEventListener('click', closeSidebar);

        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                closeSidebar();
            }
        });
    });
</script>

<script>
    // set theme based on local storage
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    const toggleThemeCheckbox = document.getElementById('toggleThemeCheckbox');
    const icon = document.getElementById('toggleThemeIcon');

    if (savedTheme === 'dark') {
        toggleThemeCheckbox.checked = true;

        icon.classList.remove('bi-sun-fill');
        icon.classList.add('bi-moon-fill');
    } else {
        toggleThemeCheckbox.checked = false;

        icon.classList.remove('bi-moon-fill');
        icon.classList.add('bi-sun-fill');
    }

    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', newTheme);

        localStorage.setItem('theme', newTheme);

        const icon = document.getElementById('toggleThemeIcon');
        if (newTheme === 'dark') {
            icon.classList.remove('bi-sun-fill');
            icon.classList.add('bi-moon-fill');
        } else {
            icon.classList.remove('bi-moon-fill');
            icon.classList.add('bi-sun-fill');
        }
    }
</script>