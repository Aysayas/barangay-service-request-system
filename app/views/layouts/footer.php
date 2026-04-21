<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
<?php $branding_assets = $branding_assets ?? []; ?>
<?php $has_primary_logo = !empty($branding_assets['primary']['exists']); ?>
<?php $has_icon_logo = !empty($branding_assets['icon']['exists']); ?>
    </main>

    <footer class="site-footer">
        <div class="app-shell flex flex-col gap-4 py-6 text-center text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between sm:text-left">
            <div class="footer-brand-lockup">
                <?php if ($has_primary_logo): ?>
                    <img
                        src="<?= e($branding_assets['primary']['url']); ?>"
                        alt="eBarangayHub"
                        class="footer-brand-logo"
                    >
                <?php elseif ($has_icon_logo): ?>
                    <img
                        src="<?= e($branding_assets['icon']['url']); ?>"
                        alt="eBarangayHub"
                        class="footer-brand-icon"
                    >
                    <div class="footer-brand-copy min-w-0">
                        <p class="font-semibold text-slate-900">eBarangayHub</p>
                        <p class="mt-1">Centralized Barangay Services, Reports, and Community Access</p>
                    </div>
                <?php else: ?>
                    <span class="brand-mark">eB</span>
                    <div class="footer-brand-copy min-w-0">
                        <p class="font-semibold text-slate-900">eBarangayHub</p>
                        <p class="mt-1">Centralized Barangay Services, Reports, and Community Access</p>
                    </div>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-500">Local civic service platform</p>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var mobileMenuButton = document.querySelector('[data-mobile-menu-toggle]');
            var mobileMenuPanel = document.querySelector('[data-mobile-menu-panel]');
            var appHeader = document.querySelector('.app-header');

            if (mobileMenuButton && mobileMenuPanel) {
                var openIcon = mobileMenuButton.querySelector('[data-mobile-menu-open-icon]');
                var closeIcon = mobileMenuButton.querySelector('[data-mobile-menu-close-icon]');

                function setMobileMenuState(isOpen) {
                    mobileMenuPanel.classList.toggle('hidden', !isOpen);
                    mobileMenuButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    document.body.classList.toggle('overflow-hidden', isOpen);

                    if (openIcon) {
                        openIcon.classList.toggle('hidden', isOpen);
                    }

                    if (closeIcon) {
                        closeIcon.classList.toggle('hidden', !isOpen);
                    }
                }

                mobileMenuButton.addEventListener('click', function () {
                    setMobileMenuState(mobileMenuPanel.classList.contains('hidden'));
                });

                mobileMenuPanel.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        setMobileMenuState(false);
                    });
                });

                mobileMenuPanel.querySelectorAll('form').forEach(function (form) {
                    form.addEventListener('submit', function () {
                        setMobileMenuState(false);
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        setMobileMenuState(false);
                    }
                });

                document.addEventListener('click', function (event) {
                    if (!mobileMenuPanel.classList.contains('hidden') && appHeader && !appHeader.contains(event.target)) {
                        setMobileMenuState(false);
                    }
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 1024) {
                        setMobileMenuState(false);
                    }
                });
            }

            document.querySelectorAll('[data-caps-lock-input]').forEach(function (input) {
                var warningKey = input.getAttribute('data-caps-lock-input');
                var warning = document.querySelector('[data-caps-lock-warning="' + warningKey + '"]');

                if (!warning) {
                    return;
                }

                function updateCapsLockWarning(event) {
                    if (!event.getModifierState) {
                        return;
                    }

                    warning.classList.toggle('hidden', !event.getModifierState('CapsLock'));
                }

                input.addEventListener('keydown', updateCapsLockWarning);
                input.addEventListener('keyup', updateCapsLockWarning);
                input.addEventListener('blur', function () {
                    warning.classList.add('hidden');
                });
            });

            document.querySelectorAll('[data-password-toggle-button]').forEach(function (button) {
                var fieldKey = button.getAttribute('data-password-toggle-button');
                var input = document.querySelector('[data-password-toggle-input="' + fieldKey + '"]');
                var eyeIcon = button.querySelector('[data-password-eye]');
                var eyeOffIcon = button.querySelector('[data-password-eye-off]');

                if (!input) {
                    return;
                }

                button.addEventListener('click', function () {
                    var shouldShow = input.type === 'password';

                    input.type = shouldShow ? 'text' : 'password';
                    button.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
                    button.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');

                    if (eyeIcon) {
                        eyeIcon.classList.toggle('hidden', shouldShow);
                    }

                    if (eyeOffIcon) {
                        eyeOffIcon.classList.toggle('hidden', !shouldShow);
                    }

                    input.focus();
                });
            });
        });
    </script>
</body>
</html>
