// Lightweight self-hosted HTML formatting toolbar for `.rich-text` textareas in Admin Settings.
// Keeps zero external CDN dependencies (GDPR & strict CSP compliant) and never touches plaintext email templates.
document.addEventListener("DOMContentLoaded", function () {
    const editors = document.querySelectorAll("textarea.rich-text");

    function wrapSelection(textarea, before, after) {
        const start = textarea.selectionStart || 0;
        const end = textarea.selectionEnd || 0;
        const value = textarea.value;
        const selected = value.slice(start, end) || "text";
        const replacement = before + selected + after;
        textarea.value = value.slice(0, start) + replacement + value.slice(end);
        textarea.focus();
        textarea.setSelectionRange(start + before.length, start + before.length + selected.length);
    }

    editors.forEach(function (textarea) {
        const toolbar = document.createElement("div");
        toolbar.className = "rte-toolbar";
        toolbar.setAttribute("role", "toolbar");
        toolbar.setAttribute("aria-label", "Text formatting");

        const actions = [
            { label: "B", title: "Bold", before: "<strong>", after: "</strong>" },
            { label: "I", title: "Italic", before: "<em>", after: "</em>" },
            { label: "P", title: "Paragraph", before: "<p>", after: "</p>" },
            { label: "• List", title: "Bullet list", before: "<ul>\n  <li>", after: "</li>\n</ul>" },
            {
                label: "Link",
                title: "Insert Link",
                custom: function () {
                    const url = window.prompt("Enter link URL (https://...):", "https://");
                    if (url && /^https?:\/\/|^mailto:|^\/[^/]/i.test(url.trim())) {
                        const safeUrl = url.trim().replace(/"/g, "&quot;");
                        wrapSelection(textarea, '<a href="' + safeUrl + '">', "</a>");
                    }
                }
            }
        ];

        actions.forEach(function (act) {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.textContent = act.label;
            btn.title = act.title;
            btn.addEventListener("click", function () {
                if (act.custom) {
                    act.custom();
                } else {
                    wrapSelection(textarea, act.before, act.after);
                }
            });
            toolbar.appendChild(btn);
        });

        textarea.classList.add("rte-has-toolbar");
        textarea.parentNode.insertBefore(toolbar, textarea);
    });
});
