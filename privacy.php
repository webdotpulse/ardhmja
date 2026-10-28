<?php
require_once __DIR__ . '/includes/db.php';

$lang = current_lang();
$page_title = __('privacy_policy') . ' - ' . $t_site_title;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <main id="main-content" class="container main-content legal-document">
        <?php if ($lang === 'sq'): ?>
            <h1>Politika e Privatësisë</h1>
            <p class="text-muted">Përditësuar së fundmi: <?= date('Y') ?></p>

            <h2>1. Kontrolluesi i të Dhënave</h2>
            <p><strong><?= htmlspecialchars($t_site_title, ENT_QUOTES, 'UTF-8') ?></strong> përpunon të dhënat personale të anëtarëve dhe vizitorëve në përputhje me Ligjin Nr. 124/2024 “Për mbrojtjen e të dhënave personale” të Republikës së Shqipërisë dhe Rregulloren e Përgjithshme për Mbrojtjen e të Dhënave të BE-së (GDPR, përfshirë Nenin 9 mbi të dhënat e mendimeve politike dhe anëtarësimit).</p>

            <h2>2. Çfarë të Dhënash Mbledhim</h2>
            <ul>
                <li><strong>Të dhënat e llogarisë dhe anëtarësimit:</strong> emri i përdoruesit, adresa e email-it, fjalëkalimi i koduar (bcrypt) dhe statusi i miratimit.</li>
                <li><strong>Pjesëmarrja qytetare dhe politike:</strong> idetë e dërguara nga anëtarët dhe mbështetjet (pajtimet) për Vlerat Themelore.</li>
                <li><strong>Profilet publike (vetëm për përfaqësuesit e miratuar):</strong> emri, roli në organizatë, biografia e shkurtër dhe fotografia e profilit kur aktivizohet shprehimisht në faqen e Profileve Publike.</li>
                <li><strong>Mesazhet e kontaktit:</strong> emri, adresa e email-it dhe përmbajtja e mesazhit të dërguar përmes formularit të kontaktit.</li>
                <li><strong>Të dhëna teknike minimale:</strong> cookie funksionale sesioni dhe gjuhe (pa cookie gjurmuese reklamash dhe pa ngarkim fontesh nga palë të treta) si dhe vlera të koduara (hash) kundër abuzimit.</li>
            </ul>

            <h2>3. Qëllimi dhe Baza Ligjore</h2>
            <p>Të dhënat përpunohen mbi bazën e pëlqimit tuaj të shprehur gjatë regjistrimit ose dërgimit të formularit të kontaktit, si dhe në kuadër të veprimtarisë së ligjshme të organizatës politike/qytetare për administrimin e anëtarësisë, moderimin e ideve dhe komunikimin me ju.</p>

            <h2>4. Ruajtja dhe Ndarja e të Dhënave</h2>
            <p>Ne nuk i shesim dhe nuk i ndajmë të dhënat tuaja personale me palë të treta për qëllime tregtare. Të dhënat ruhen në infrastrukturë të sigurt hostimi në Bashkimin Evropian (Combell) vetëm për aq kohë sa mbani llogarinë aktive ose sa nevojitet për t’iu përgjigjur kërkesës suaj.</p>

            <h2>5. Të Drejtat Tuaja</h2>
            <ul>
                <li>Të kërkoni qasje, korrigjim ose përditësim të emrit të përdoruesit, email-it dhe fjalëkalimit direkt nga <strong>Profili</strong> juaj.</li>
                <li>Të tërhiqni në çdo kohë mbështetjen për Vlerat Themelore ose idetë në pritje.</li>
                <li>Të fshini përgjithmonë llogarinë dhe të gjitha të dhënat tuaja në çdo kohë përmes opsionit <strong>Fshi Llogarinë</strong> në profilin tuaj, ose duke na shkruar përmes faqes <a href="contact.php">Kontakt</a>.</li>
            </ul>
        <?php else: ?>
            <h1>Privacy Policy</h1>
            <p class="text-muted">Last updated: <?= date('Y') ?></p>

            <h2>1. Data Controller</h2>
            <p><strong><?= htmlspecialchars($t_site_title, ENT_QUOTES, 'UTF-8') ?></strong> processes personal data of members and visitors in accordance with Albanian Law No. 124/2024 on the Protection of Personal Data and the EU General Data Protection Regulation (GDPR, including Article 9 governing political opinions and party membership data).</p>

            <h2>2. Data We Collect</h2>
            <ul>
                <li><strong>Account &amp; Membership Data:</strong> username, email address, hashed password (bcrypt), and membership approval status.</li>
                <li><strong>Civic &amp; Political Participation:</strong> community ideas you submit and endorsements of Core Values.</li>
                <li><strong>Public Representative Profiles (opt-in):</strong> full name, organizational role, short biography, and profile photo when explicitly published on the Public Profiles page.</li>
                <li><strong>Contact Inquiries:</strong> name, email address, and message content submitted through our Contact form.</li>
                <li><strong>Strictly Necessary Technical Data:</strong> first-party session and language preference cookies (no third-party advertising trackers or external font CDNs) and irreversibly hashed anti-abuse rate-limit counters.</li>
            </ul>

            <h2>3. Purpose &amp; Legal Basis</h2>
            <p>Your personal data is processed on the basis of your explicit consent provided upon registration or contact submission, and within our legitimate civic and political activities to manage membership, moderate community proposals, and communicate important account updates.</p>

            <h2>4. Hosting, Retention &amp; Transfers</h2>
            <p>We never sell or rent member or visitor data. Data is hosted on European Union infrastructure (Combell) and retained only while your membership account remains active or for as long as needed to resolve your contact inquiry.</p>

            <h2>5. Your Rights</h2>
            <ul>
                <li>Access and update your username, email address, and password directly from your <strong>Profile</strong>.</li>
                <li>Withdraw Core Value endorsements or pending idea submissions at any time.</li>
                <li>Exercise your Right to Erasure immediately using the self-service <strong>Delete Account</strong> page in your profile, or by reaching out via our <a href="contact.php">Contact</a> page.</li>
            </ul>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
