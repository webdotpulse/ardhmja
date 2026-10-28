// Main UI interactions for AsamblejaKombetare (CSP-clean, zero inline handlers)
document.addEventListener("DOMContentLoaded", function () {
    // 1. Accessible mobile navigation toggle
    const nav = document.getElementById("primary-navigation") || document.querySelector(".main-nav");
    const toggleBtn = document.querySelector(".mobile-menu-toggle");

    if (nav && toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            const isOpen = nav.classList.toggle("nav-open");
            toggleBtn.setAttribute("aria-expanded", isOpen ? "true" : "false");
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape" && nav.classList.contains("nav-open")) {
                nav.classList.remove("nav-open");
                toggleBtn.setAttribute("aria-expanded", "false");
                toggleBtn.focus();
            }
        });
    }

    // 2. Unified confirmation prompts via `data-confirm` (prevents double dialogs & satisfies strict CSP)
    document.addEventListener("click", function (e) {
        const trigger = e.target.closest("button[data-confirm], a[data-confirm]");
        if (!trigger) return;
        const message = trigger.getAttribute("data-confirm") || "Are you sure?";
        if (!window.confirm(message)) {
            e.preventDefault();
        }
    });

    // 3. Accessible Show / Hide Password toggles
    const pwToggles = document.querySelectorAll(".btn-password-toggle");
    pwToggles.forEach(function (btn) {
        btn.addEventListener("click", function () {
            const targetId = btn.getAttribute("data-target");
            const input = targetId ? document.getElementById(targetId) : btn.parentElement.querySelector("input");
            if (!input) return;
            const reveal = input.type === "password";
            input.type = reveal ? "text" : "password";
            btn.setAttribute("aria-pressed", reveal ? "true" : "false");
            const showLabel = btn.getAttribute("data-show-text") || "Show";
            const hideLabel = btn.getAttribute("data-hide-text") || "Hide";
            btn.textContent = reveal ? hideLabel : showLabel;
        });
    });

    // 4. Prevent accidental double-submission on valid POST forms (only when no multi-action buttons)
    const forms = document.querySelectorAll("form[method='POST'], form[method='post']");
    forms.forEach(function (form) {
        form.addEventListener("submit", function () {
            if (typeof form.checkValidity === "function" && !form.checkValidity()) {
                return;
            }
            const submitBtns = form.querySelectorAll("button[type='submit']:not([name])");
            if (submitBtns.length === 1) {
                window.setTimeout(function () {
                    submitBtns[0].disabled = true;
                }, 0);
            }
        });
    });

    // 5. Language tabs in Admin forms (e.g., EN / SQ panels)
    const tabButtons = document.querySelectorAll(".lang-tab-btn[data-lang-tab]");
    if (tabButtons.length > 0) {
        tabButtons.forEach(function (btn) {
            btn.addEventListener("click", function () {
                const targetLang = btn.getAttribute("data-lang-tab");
                tabButtons.forEach(function (b) {
                    const active = b.getAttribute("data-lang-tab") === targetLang;
                    b.classList.toggle("active", active);
                    b.setAttribute("aria-selected", active ? "true" : "false");
                });
                document.querySelectorAll("[data-lang-panel]").forEach(function (panel) {
                    panel.hidden = panel.getAttribute("data-lang-panel") !== targetLang;
                });
            });
        });
    }
});
