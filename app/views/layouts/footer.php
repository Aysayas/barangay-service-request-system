<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
    </main>

    <footer class="border-t border-slate-200/80 bg-white/90">
        <div class="app-shell flex flex-col gap-2 py-6 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-semibold text-slate-900">eBarangayHub</p>
                <p class="mt-1">Centralized Barangay Services, Reports, and Community Access</p>
            </div>
            <p class="text-xs text-slate-500">Local civic service platform</p>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
