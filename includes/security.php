<?php
/**
 * Centralized security utilities for AsamblejaKombetare:
 * - Production error handling & hardened session initialization
 * - HTTP Security Headers & per-request CSP nonces
 * - Fetch Metadata cross-site POST protection
 * - Synchronizer CSRF tokens & accessible honeypot anti-spam
 * - Privacy-preserving (hashed IP) database rate limiting
 * - Strict DOM allowlist HTML sanitizer (UTF-8 safe)
 * - Live database session/role validation
 */

function is_https_request(): bool {
    if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https') {
        return true;
    }
    if ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443) {
        return true;
    }
    return false;
}

function csp_nonce(): string {
    static $nonce = null;
    if ($nonce === null) {
        $nonce = base64_encode(random_bytes(16));
    }
    return $nonce;
}

function init_session_and_headers(): void {
    static $initialized = false;
    if ($initialized) {
        return;
    }
    $initialized = true;

    $debug = defined('APP_DEBUG') && APP_DEBUG === true;
    if (!$debug) {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        ini_set('log_errors', '1');
    }

    $https = is_https_request();

    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    } elseif (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    if (!headers_sent()) {
        $nonce = csp_nonce();
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "frame-ancestors 'self'",
            "form-action 'self'",
        ]);

        header("Content-Security-Policy: {$csp}");
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), geolocation=(), microphone=()');
        header('Cross-Origin-Opener-Policy: same-origin-allow-popups');
        header('Vary: Sec-Fetch-Dest, Sec-Fetch-Mode, Sec-Fetch-Site');

        if ($https) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }

    enforce_fetch_metadata();
}

function enforce_fetch_metadata(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }
    $site = strtolower((string)($_SERVER['HTTP_SEC_FETCH_SITE'] ?? ''));
    if ($site !== '' && !in_array($site, ['same-origin', 'same-site', 'none'], true)) {
        if (!headers_sent()) {
            http_response_code(403);
        }
        exit('Forbidden: Cross-site request blocked.');
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(?string $submitted = null): bool {
    $submitted = $submitted ?? (string)($_POST['csrf_token'] ?? '');
    if ($submitted === '' || empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submitted);
}

function honeypot_field(): string {
    return '<div class="visually-hidden" aria-hidden="true">'
        . '<label for="ak_website_hp">Website</label>'
        . '<input type="text" id="ak_website_hp" name="website_url" value="" tabindex="-1" autocomplete="off">'
        . '</div>';
}

function verify_honeypot(): bool {
    return empty($_POST['website_url']);
}

function client_ip_hash(): string {
    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    $salt = defined('DB_NAME') ? DB_NAME : 'asamblejakombetare_salt';
    return hash('sha256', $ip . '|' . $salt . '|' . date('Y-m-d'));
}

function check_rate_limit(?PDO $pdo, string $action, int $max_attempts, int $window_seconds, string $extra_key = ''): bool {
    if (!$pdo) {
        return true;
    }
    try {
        // Opportunistic cleanup (1 in 25 checks)
        if (random_int(1, 25) === 1) {
            $pdo->exec("DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 1 DAY)");
        }
        $key_hash = hash('sha256', client_ip_hash() . '|' . strtolower(trim($extra_key)));
        $cutoff = date('Y-m-d H:i:s', time() - $window_seconds);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE action = ? AND ip_hash = ? AND created_at >= ?");
        $stmt->execute([$action, $key_hash, $cutoff]);
        return ((int)$stmt->fetchColumn()) < $max_attempts;
    } catch (Throwable $e) {
        error_log('Rate limit check error: ' . $e->getMessage());
        return true;
    }
}

function record_rate_limit(?PDO $pdo, string $action, string $extra_key = ''): void {
    if (!$pdo) {
        return;
    }
    try {
        $key_hash = hash('sha256', client_ip_hash() . '|' . strtolower(trim($extra_key)));
        $stmt = $pdo->prepare("INSERT INTO rate_limits (action, ip_hash, created_at) VALUES (?, ?, ?)");
        $stmt->execute([$action, $key_hash, date('Y-m-d H:i:s')]);
    } catch (Throwable $e) {
        error_log('Rate limit record error: ' . $e->getMessage());
    }
}

function app_base_url(): string {
    if (defined('APP_URL') && APP_URL !== '' && APP_URL !== 'https://yourdomain.com') {
        return rtrim(APP_URL, '/');
    }

    $raw_host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
    if (!preg_match('/^[a-zA-Z0-9.-]+(?::[0-9]{1,5})?$/', $raw_host)) {
        $raw_host = 'localhost';
    }

    $is_local = (strpos($raw_host, 'localhost') === 0 || strpos($raw_host, '127.0.0.1') === 0);
    $scheme = ($is_local && !is_https_request()) ? 'http' : 'https';

    $script_dir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
    if (preg_match('#/admin$#', $script_dir)) {
        $script_dir = dirname($script_dir);
    }
    $script_dir = rtrim($script_dir, '/');
    if ($script_dir === '.' || $script_dir === '/') {
        $script_dir = '';
    }

    return $scheme . '://' . $raw_host . $script_dir;
}

function sanitize_rich_html(?string $html): string {
    $html = trim((string)$html);
    if ($html === '') {
        return '';
    }

    // If plain text with no HTML tags, escape and preserve line breaks cleanly
    if (strpos($html, '<') === false) {
        return nl2br(htmlspecialchars($html, ENT_QUOTES, 'UTF-8'), false);
    }

    $allowed_tags = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'h3' => [],
        'h4' => [],
        'blockquote' => [],
        'a' => ['href'],
    ];

    $drop_entirely = [
        'script', 'style', 'iframe', 'object', 'embed', 'svg', 'math',
        'form', 'input', 'button', 'textarea', 'select', 'meta', 'link', 'base', 'head', 'title',
    ];

    $encoded = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
    $prev = libxml_use_internal_errors(true);
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadHTML('<div id="__ak_sanitize_root__">' . $encoded . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $root = $dom->getElementById('__ak_sanitize_root__');
    if (!$root) {
        return htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8');
    }

    $clean_node = function (DOMNode $node) use (&$clean_node, $allowed_tags, $drop_entirely, $dom): void {
        if (!$node->hasChildNodes()) {
            return;
        }
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
                continue;
            }
            if ($child->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            /** @var DOMElement $child */
            $tag = strtolower($child->tagName);

            if (in_array($tag, $drop_entirely, true)) {
                $node->removeChild($child);
                continue;
            }

            $clean_node($child);

            if (!isset($allowed_tags[$tag])) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            // Strip disallowed attributes
            $allowed_attrs = $allowed_tags[$tag];
            $attrs_to_remove = [];
            if ($child->hasAttributes()) {
                foreach ($child->attributes as $attr) {
                    $attr_name = strtolower($attr->name);
                    if (!in_array($attr_name, $allowed_attrs, true)) {
                        $attrs_to_remove[] = $attr->name;
                    }
                }
            }
            foreach ($attrs_to_remove as $attr_name) {
                $child->removeAttribute($attr_name);
            }

            if ($tag === 'a') {
                $href = trim($child->getAttribute('href'));
                if ($href === '' || !preg_match('#^(https?://|mailto:|/[^/])#i', $href)) {
                    $child->removeAttribute('href');
                } else {
                    $child->setAttribute('rel', 'noopener noreferrer');
                }
            }
        }
    };

    $clean_node($root);

    $output = '';
    foreach ($root->childNodes as $child) {
        $output .= $dom->saveHTML($child);
    }

    return trim(mb_decode_numericentity($output, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8'));
}

function validate_password_strength(string $password): bool {
    return strlen($password) >= 8
        && (bool)preg_match('/[A-Z]/', $password)
        && (bool)preg_match('/[a-z]/', $password)
        && (bool)preg_match('/[0-9]/', $password);
}

function validate_username_format(string $username): bool {
    $len = mb_strlen($username, 'UTF-8');
    if ($len < 3 || $len > 50) {
        return false;
    }
    return (bool)preg_match('/^[\p{L}\p{N}_.\-]+$/u', $username);
}

function destroy_user_session(bool $send_clear_site_data = true): void {
    $_SESSION = [];

    if (ini_get('session.use_cookies') && !headers_sent()) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'] ?: '/',
            'domain' => $params['domain'] ?: '',
            'secure' => (bool)$params['secure'],
            'httponly' => (bool)$params['httponly'],
            'samesite' => $params['samesite'] ?: 'Lax',
        ]);
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    if ($send_clear_site_data && is_https_request() && !headers_sent()) {
        header('Clear-Site-Data: "cookies", "storage"');
    }
}

function refresh_authenticated_user(?PDO $pdo): ?array {
    if (empty($_SESSION['user_id']) || !$pdo) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT id, username, email, role, status, email_verified, profile_picture FROM users WHERE id = ?");
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || $user['status'] === 'rejected' || (int)$user['email_verified'] === 0) {
        destroy_user_session(false);
        return null;
    }

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['status'] = $user['status'];

    return $user;
}

function delete_uploaded_avatar(?string $filename, string $uploads_dir): void {
    if (!$filename) {
        return;
    }
    $safe_name = basename($filename);
    if ($safe_name === '' || $safe_name === '.' || $safe_name === '..') {
        return;
    }
    $full_path = rtrim($uploads_dir, '/\\') . DIRECTORY_SEPARATOR . $safe_name;
    if (is_file($full_path)) {
        @unlink($full_path);
    }
}
