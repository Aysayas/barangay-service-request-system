<?php defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed'); ?>
    </main>

    <footer class="border-t border-zinc-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-5 text-sm text-zinc-600">
            eBarangayHub - Centralized Barangay Services, Reports, and Community Access
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
