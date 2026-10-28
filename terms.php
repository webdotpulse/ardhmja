<?php
require_once __DIR__ . '/includes/db.php';

$lang = current_lang();
$page_title = __('terms_of_use') . ' - ' . $t_site_title;
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
            <h1>Kushtet e Përdorimit</h1>
            <p class="text-muted">Përditësuar së fundmi: <?= date('Y') ?></p>

            <h2>1. Pranimi i Kushteve</h2>
            <p>Duke u regjistruar ose duke përdorur platformën e <strong><?= htmlspecialchars($t_site_title, ENT_QUOTES, 'UTF-8') ?></strong>, ju pranoni t’u përmbaheni këtyre Kushteve të Përdorimit dhe parimeve të dialogut qytetar e demokratik.</p>

            <h2>2. Regjistrimi dhe Siguria e Llogarisë</h2>
            <ul>
                <li>Ju duhet të jepni një adresë të saktë email-i dhe ta verifikoni atë për të aktivizuar hyrjen në llogari.</li>
                <li>Pjesëmarrja në dërgimin e ideve dhe mbështetja e Vlerave Themelore u mundësohet anëtarëve të miratuar nga administrata.</li>
                <li>Ju jeni përgjegjës për ruajtjen e fshehtësisë së fjalëkalimit tuaj.</li>
            </ul>

            <h2>3. Rregullat e Komunitetit dhe Dërgimi i Ideve</h2>
            <ul>
                <li>Idetë dhe mesazhet duhet të jenë konstruktive, të respektueshme dhe në përputhje me ligjin.</li>
                <li>Ndalohet gjuha e urrejtjes, shpifja, kërcënimet, publikimi i të dhënave personale të personave të tjerë pa leje, si dhe mesazhet e padëshiruara (spam).</li>
                <li>Administrata rezervon të drejtën të shqyrtojë, miratojë ose refuzojë propozimet para publikimit, si dhe të pezullojë llogaritë që shkelin këto kushte.</li>
            </ul>

            <h2>4. Të Drejtat mbi Përmbajtjen</h2>
            <p>Duke dërguar një ide për publikim në platformë, ju i jepni <?= htmlspecialchars($t_site_title, ENT_QUOTES, 'UTF-8') ?> lejen jo-ekskluzive për ta shfaqur publikisht atë së bashku me emrin tuaj të përdoruesit, derisa ju ta tërhiqni ose të fshini llogarinë tuaj.</p>
        <?php else: ?>
            <h1>Terms of Use</h1>
            <p class="text-muted">Last updated: <?= date('Y') ?></p>

            <h2>1. Acceptance of Terms</h2>
            <p>By registering on or using the <strong><?= htmlspecialchars($t_site_title, ENT_QUOTES, 'UTF-8') ?></strong> platform, you agree to abide by these Terms of Use and our standards for respectful democratic participation.</p>

            <h2>2. Account Registration &amp; Security</h2>
            <ul>
                <li>You must register with a valid email address and complete email verification to sign in.</li>
                <li>Submitting community ideas and endorsing Core Values requires an approved member account.</li>
                <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
            </ul>

            <h2>3. Community Standards &amp; Idea Moderation</h2>
            <ul>
                <li>All proposals and messages must remain constructive, lawful, and respectful.</li>
                <li>Hate speech, harassment, defamation, disclosure of third-party private information, and automated spam are strictly prohibited.</li>
                <li>Administrators review submitted ideas prior to publication and may reject submissions or revoke accounts that violate these rules.</li>
            </ul>

            <h2>4. User Contributions</h2>
            <p>By submitting an idea for publication, you grant <?= htmlspecialchars($t_site_title, ENT_QUOTES, 'UTF-8') ?> a non-exclusive license to display your proposal publicly alongside your username until you delete your account or request removal.</p>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/includes/footer.php'; ?>
    <script nonce="<?= htmlspecialchars(csp_nonce(), ENT_QUOTES, 'UTF-8') ?>" src="<?= htmlspecialchars(asset_url('assets/js/main.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
