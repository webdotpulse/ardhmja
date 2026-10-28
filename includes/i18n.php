<?php
/**
 * Bilingual (English `en` & Albanian `sq`) localization helpers for AsamblejaKombetare.
 */

function supported_languages(): array {
    return ['sq' => 'Shqip', 'en' => 'English'];
}

function init_language(): string {
    $supported = supported_languages();

    if (isset($_GET['lang']) && is_string($_GET['lang'])) {
        $requested = strtolower(trim($_GET['lang']));
        if (isset($supported[$requested])) {
            $_SESSION['lang'] = $requested;
            if (!headers_sent()) {
                $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
                setcookie('ak_lang', $requested, [
                    'expires' => time() + 31536000,
                    'path' => '/',
                    'secure' => $secure,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
            return $requested;
        }
    }

    if (!empty($_SESSION['lang']) && isset($supported[$_SESSION['lang']])) {
        return $_SESSION['lang'];
    }

    if (!empty($_COOKIE['ak_lang']) && isset($supported[$_COOKIE['ak_lang']])) {
        $_SESSION['lang'] = $_COOKIE['ak_lang'];
        return $_COOKIE['ak_lang'];
    }

    $accept = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '');
    if (strpos($accept, 'sq') !== false) {
        $_SESSION['lang'] = 'sq';
        return 'sq';
    }

    $_SESSION['lang'] = 'sq';
    return 'sq';
}

function current_lang(): string {
    global $app_lang;
    if (!empty($app_lang)) {
        return $app_lang;
    }
    $app_lang = init_language();
    return $app_lang;
}

function lang_switch_url(string $target_lang): string {
    $uri = $_SERVER['REQUEST_URI'] ?? 'index.php';
    $parts = parse_url($uri);
    $path = $parts['path'] ?? 'index.php';
    $query = [];
    if (!empty($parts['query'])) {
        parse_str($parts['query'], $query);
    }
    $query['lang'] = $target_lang;
    return $path . '?' . http_build_query($query);
}

function format_date_localized(?string $datetime, ?string $lang = null): string {
    if (!$datetime) {
        return '';
    }
    $ts = strtotime($datetime);
    if ($ts === false) {
        return htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8');
    }
    $lang = $lang ?? current_lang();
    if ($lang === 'sq') {
        $months_sq = [
            1 => 'Jan', 2 => 'Shk', 3 => 'Mar', 4 => 'Pri',
            5 => 'Maj', 6 => 'Qer', 7 => 'Kor', 8 => 'Gus',
            9 => 'Sht', 10 => 'Tet', 11 => 'Nën', 12 => 'Dhj',
        ];
        $m = (int)date('n', $ts);
        return date('d', $ts) . ' ' . ($months_sq[$m] ?? date('M', $ts)) . ' ' . date('Y', $ts);
    }
    return date('M d, Y', $ts);
}

function translations_catalog(): array {
    static $catalog = null;
    if ($catalog !== null) {
        return $catalog;
    }

    $catalog = [
        'en' => [
            // Navigation & Common
            'skip_to_content' => 'Skip to main content',
            'toggle_nav' => 'Toggle navigation menu',
            'lang_label' => 'Language',
            'home' => 'Home',
            'back' => 'Back',
            'save_changes' => 'Save Changes',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
            'edit' => 'Edit',
            'submit' => 'Submit',
            'confirm_delete' => 'Are you sure you want to delete this item?',
            'privacy_policy' => 'Privacy Policy',
            'terms_of_use' => 'Terms of Use',
            'page_prev' => 'Previous',
            'page_next' => 'Next',
            'page_of' => 'Page {page} of {total}',
            'db_unavailable_title' => 'Service Temporarily Unavailable',
            'db_unavailable_text' => 'We are performing a brief maintenance check or experiencing high traffic. Please try again in a few moments.',
            'csrf_error' => 'Your session expired or the form token was invalid. Please submit the form again.',
            'rate_limit_error' => 'Too many attempts. Please wait a few minutes before trying again.',
            'show_password' => 'Show',
            'hide_password' => 'Hide',
            'show_password_aria' => 'Show password as plain text',

            // Home & Public Pages
            'no_ideas_yet' => 'No ideas published yet.',
            'by_author_on_date' => 'By {author} on {date}',
            'login_to_agree_info' => 'Log in with an approved member account to express your support for our Core Values.',
            'i_agree' => 'I agree',
            'agreed_badge' => '✓ You support this',
            'withdraw_agreement' => 'Withdraw',
            'approval_pending_to_agree' => 'Account approval pending to express support.',
            'no_core_values' => 'No core values available at the moment.',
            'members_agree_one' => '1 member agrees',
            'members_agree_many' => '{count} members agree',
            'agreed_flash' => 'Thank you for supporting this Core Value!',
            'withdrawn_flash' => 'Your support for this Core Value has been withdrawn.',
            'public_profiles_intro' => 'Meet our leadership, team members, and public representatives.',
            'no_public_profiles' => 'No public profiles available at the moment.',
            'no_image' => 'No Photo',
            'role_admin' => 'Leadership / Admin',
            'role_member' => 'Member',

            // Idea Submission
            'submit_idea_intro' => 'Share your proposal with AsamblejaKombetare. Submissions are reviewed by an administrator before publication.',
            'idea_title_label' => 'Idea Title',
            'idea_desc_label' => 'Proposal Description',
            'idea_submitted_success' => 'Your idea has been submitted and is waiting for administrator review.',
            'idea_submitted_error' => 'Failed to submit idea. Please try again.',
            'idea_length_error' => 'Title must be between 5 and 200 characters, and description between 20 and 5,000 characters.',
            'return_to_profile' => 'Return to Profile',

            // Auth: Register, Login, Verify, Password Reset
            'username_label' => 'Username',
            'username_hint' => '3–50 characters (letters, numbers, underscores, hyphens, or dots).',
            'email_label' => 'Email Address',
            'password_label' => 'Password',
            'password_hint' => 'At least 8 characters, including an uppercase letter, a lowercase letter, and a number.',
            'register_btn' => 'Create Account',
            'already_have_account' => 'Already have an account?',
            'login_here' => 'Log in here',
            'dont_have_account' => "Don't have an account?",
            'register_here' => 'Register here',
            'login_btn' => 'Log In',
            'forgot_password_link' => 'Forgot your password?',
            'resend_verification_link' => 'Need a new verification email?',
            'fill_all_fields' => 'Please fill in all required fields.',
            'invalid_email' => 'Please enter a valid email address.',
            'invalid_username' => 'Username must be 3–50 characters and contain only letters, numbers, underscores, dots, or hyphens.',
            'weak_password' => 'Password must be at least 8 characters long and include uppercase, lowercase, and a number.',
            'consent_checkbox_html' => 'I have read and agree to the <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a> (including processing of membership data) and <a href="terms.php" target="_blank" rel="noopener">Terms of Use</a>.',
            'consent_required' => 'You must accept the Privacy Policy and Terms of Use to continue.',
            'user_or_email_exists' => 'An account with this username or email already exists.',
            'register_success' => 'Registration successful! Please check your email inbox (and spam folder) to verify your email address.',
            'register_mail_fallback' => 'Your account was created, but we could not send the verification email right now. Please try "Resend verification email" shortly or contact us.',
            'generic_error' => 'Something went wrong. Please try again later.',
            'login_invalid' => 'Invalid email or password.',
            'login_rejected' => 'Your membership application was not approved.',
            'login_unverified' => 'Please verify your email address before logging in.',

            // Email Verification & Resend
            'verify_title' => 'Email Verification',
            'verify_success' => 'Your email has been verified! You can now log in to check your membership approval status.',
            'verify_invalid' => 'Invalid or expired verification link. Your email may already be verified, or you can request a new link below.',
            'verify_missing' => 'No verification token was provided.',
            'go_to_login' => 'Go to Login',
            'resend_title' => 'Resend Verification Email',
            'resend_intro' => 'Enter the email address you registered with and we will send a fresh verification link if your account is still unverified.',
            'resend_btn' => 'Send Verification Link',
            'resend_sent_generic' => 'If an unverified account exists for that email address, a new verification link has been sent.',

            // Forgot / Reset Password
            'forgot_title' => 'Reset Your Password',
            'forgot_intro' => 'Enter your account email address and we will send you a secure link to choose a new password.',
            'forgot_btn' => 'Send Reset Link',
            'forgot_sent_generic' => 'If an account exists for that email address, password reset instructions have been sent.',
            'reset_title' => 'Choose a New Password',
            'new_password_label' => 'New Password',
            'confirm_password_label' => 'Confirm New Password',
            'reset_btn' => 'Save New Password',
            'reset_invalid_token' => 'This password reset link is invalid or has expired. Please request a new one.',
            'passwords_mismatch' => 'The new passwords do not match.',
            'reset_success' => 'Your password has been updated. You can now log in with your new password.',

            // Profile & Account Settings
            'welcome_user' => 'Welcome, {username}',
            'change_username' => 'Change Username',
            'change_email' => 'Change Email',
            'change_password' => 'Change Password',
            'delete_account' => 'Delete Account',
            'account_status_label' => 'Account Status:',
            'status_pending' => 'Pending',
            'status_approved' => 'Approved',
            'status_rejected' => 'Rejected',
            'pending_account_notice' => 'Your account is awaiting administrator approval. Once approved, you will be able to submit ideas and endorse Core Values.',
            'my_submitted_ideas' => 'My Submitted Ideas',
            'submit_new_idea' => 'Submit New Idea',
            'no_user_ideas' => "You haven't submitted any ideas yet.",
            'delete_pending_idea' => 'Withdraw',
            'idea_deleted_flash' => 'Your pending idea has been removed.',
            'agreed_values_heading' => 'Core Values I Support',
            'no_agreed_values' => "You haven't endorsed any Core Values yet.",
            'current_password_label' => 'Current Password',
            'incorrect_current_password' => 'Incorrect current password.',
            'password_changed_success' => 'Password changed successfully.',
            'new_username_label' => 'New Username',
            'username_taken' => 'That username is already taken.',
            'username_changed_success' => 'Username changed successfully.',
            'current_email_label' => 'Current Email',
            'new_email_label' => 'New Email Address',
            'email_taken' => 'That email address is already registered.',
            'email_changed_admin_success' => 'Your email address has been updated.',
            'email_changed_user_success' => 'Your email address has been updated. Please check your inbox to verify your new email address before your next login.',
            'delete_account_title' => 'Delete My Account',
            'delete_account_warning' => 'Deleting your account permanently removes your profile, submitted ideas, and Core Value endorsements. This action cannot be undone.',
            'delete_account_confirm_label' => 'Enter your current password to confirm permanent deletion',
            'delete_account_btn' => 'Permanently Delete My Account',
            'delete_last_admin_error' => 'You cannot delete the last remaining administrator account.',
            'account_deleted_msg' => 'Your account and associated personal data have been permanently deleted.',

            // Contact Page
            'contact_name_label' => 'Full Name',
            'contact_email_label' => 'Email Address',
            'contact_message_label' => 'Message',
            'contact_send_btn' => 'Send Message',
            'contact_consent_html' => 'I agree that AsamblejaKombetare may use the details provided above to respond to my inquiry in accordance with the <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>.',
            'contact_success' => 'Thank you! Your message has been received and our team will get back to you.',
            'contact_length_error' => 'Please enter a valid name (max 100 chars) and message (10–5,000 chars).',

            // Error Pages
            '404_title' => 'Page Not Found (404)',
            '404_text' => 'The page you are looking for may have been moved or no longer exists.',
            '500_title' => 'Unexpected Error (500)',
            '500_text' => 'An unexpected error occurred while processing your request. Please try again later.',
            'back_home_btn' => 'Back to Home',

            // Outgoing Emails
            'email_reset_subject' => 'Password reset for {site_title}',
            'email_reset_body' => "Hello {username},\n\nWe received a request to reset your password on {site_title}.\nClick the link below within 60 minutes to choose a new password:\n{reset_link}\n\nIf you did not request this change, you can safely ignore this email.",
            'email_account_approved_subject' => 'Your {site_title} membership has been approved',
            'email_account_approved_body' => "Hello {username},\n\nGreat news! Your membership account on {site_title} has been approved.\nYou can now log in to submit community ideas and endorse Core Values:\n{login_link}\n\nBest regards,\n{site_title}",
            'email_idea_approved_subject' => 'Your idea on {site_title} has been published',
            'email_idea_approved_body' => "Hello {username},\n\nYour submitted idea \"{idea_title}\" has been approved and is now published on {site_title}:\n{ideas_link}\n\nThank you for contributing!\n{site_title}",
        ],

        'sq' => [
            // Navigation & Common
            'skip_to_content' => 'Kalo te përmbajtja kryesore',
            'toggle_nav' => 'Hap ose mbyll menynë e navigimit',
            'lang_label' => 'Gjuha',
            'home' => 'Ballina',
            'back' => 'Kthehu',
            'save_changes' => 'Ruaj Ndryshimet',
            'cancel' => 'Anulo',
            'delete' => 'Fshi',
            'edit' => 'Ndrysho',
            'submit' => 'Dërgo',
            'confirm_delete' => 'A jeni i sigurt që dëshironi ta fshini këtë element?',
            'privacy_policy' => 'Politika e Privatësisë',
            'terms_of_use' => 'Kushtet e Përdorimit',
            'page_prev' => 'Para',
            'page_next' => 'Pas',
            'page_of' => 'Faqja {page} nga {total}',
            'db_unavailable_title' => 'Shërbimi Përkohësisht i Padisponueshëm',
            'db_unavailable_text' => 'Po kryejmë një mirëmbajtje të shkurtër teknike. Ju lutemi provoni përsëri pas pak çastesh.',
            'csrf_error' => 'Sesioni juaj ka skaduar ose formulari nuk ishte i vlefshëm. Ju lutemi provoni përsëri.',
            'rate_limit_error' => 'Shumë tentativa brenda një kohe të shkurtër. Ju lutemi prisni disa minuta para se të provoni përsëri.',
            'show_password' => 'Shfaq',
            'hide_password' => 'Fshih',
            'show_password_aria' => 'Shfaq fjalëkalimin si tekst',

            // Home & Public Pages
            'no_ideas_yet' => 'Ende nuk ka ide të publikuara.',
            'by_author_on_date' => 'Nga {author} më {date}',
            'login_to_agree_info' => 'Hyni me një llogari të miratuar për të shprehur mbështetjen tuaj për Vlerat Themelore.',
            'i_agree' => 'Pajtohem',
            'agreed_badge' => '✓ E mbështes këtë',
            'withdraw_agreement' => 'Tërhiq',
            'approval_pending_to_agree' => 'Llogaria është në pritje të miratimit për të shprehur mbështetjen.',
            'no_core_values' => 'Nuk ka vlera themelore të publikuara për momentin.',
            'members_agree_one' => '1 anëtar pajtohet',
            'members_agree_many' => '{count} anëtarë pajtohen',
            'agreed_flash' => 'Faleminderit për mbështetjen e kësaj Vlere Themelore!',
            'withdrawn_flash' => 'Mbështetja juaj për këtë Vlerë Themelore u tërhoq.',
            'public_profiles_intro' => 'Njihuni me drejtuesit, anëtarët e ekipit dhe përfaqësuesit tanë publikë.',
            'no_public_profiles' => 'Nuk ka profile publike për momentin.',
            'no_image' => 'Pa Foto',
            'role_admin' => 'Drejtues / Administrator',
            'role_member' => 'Anëtar',

            // Idea Submission
            'submit_idea_intro' => 'Ndani vizionin dhe propozimin tuaj me AsamblejaKombetare. Çdo dorëzim shqyrtohet nga administrata para publikimit.',
            'idea_title_label' => 'Titulli i Idesë',
            'idea_desc_label' => 'Përshkrimi i Propozimit',
            'idea_submitted_success' => 'Ideja juaj u dërgua me sukses dhe është në pritje të miratimit.',
            'idea_submitted_error' => 'Dërgimi i idesë dështoi. Ju lutemi provoni përsëri.',
            'idea_length_error' => 'Titulli duhet të ketë 5–200 karaktere dhe përshkrimi 20–5.000 karaktere.',
            'return_to_profile' => 'Kthehu te Profili',

            // Auth: Register, Login, Verify, Password Reset
            'username_label' => 'Emri i përdoruesit',
            'username_hint' => '3–50 karaktere (shkronja, numra, nënvizime, viza ose pika).',
            'email_label' => 'Adresa e Email-it',
            'password_label' => 'Fjalëkalimi',
            'password_hint' => 'Të paktën 8 karaktere, përfshirë një shkronjë të madhe, një të vogël dhe një numër.',
            'register_btn' => 'Krijo Llogarinë',
            'already_have_account' => 'Keni tashmë një llogari?',
            'login_here' => 'Hyni këtu',
            'dont_have_account' => 'Nuk keni ende një llogari?',
            'register_here' => 'Regjistrohuni këtu',
            'login_btn' => 'Hyr',
            'forgot_password_link' => 'Keni harruar fjalëkalimin?',
            'resend_verification_link' => 'Keni nevojë për një email të ri verifikimi?',
            'fill_all_fields' => 'Ju lutemi plotësoni të gjitha fushat e detyrueshme.',
            'invalid_email' => 'Ju lutemi shkruani një adresë të vlefshme email-i.',
            'invalid_username' => 'Emri i përdoruesit duhet të ketë 3–50 karaktere dhe të përmbajë vetëm shkronja, numra, nënvizime, pika ose viza.',
            'weak_password' => 'Fjalëkalimi duhet të ketë të paktën 8 karaktere, një shkronjë të madhe, një të vogël dhe një numër.',
            'consent_checkbox_html' => 'Kam lexuar dhe pranoj <a href="privacy.php" target="_blank" rel="noopener">Politikën e Privatësisë</a> (përfshirë përpunimin e të dhënave të anëtarësimit) dhe <a href="terms.php" target="_blank" rel="noopener">Kushtet e Përdorimit</a>.',
            'consent_required' => 'Ju duhet të pranoni Politikën e Privatësisë dhe Kushtet e Përdorimit për të vazhduar.',
            'user_or_email_exists' => 'Një llogari me këtë emër përdoruesi ose email ekziston tashmë.',
            'register_success' => 'Regjistrimi u krye me sukses! Ju lutemi kontrolloni email-in tuaj (edhe dosjen Spam) për të verifikuar llogarinë.',
            'register_mail_fallback' => 'Llogaria juaj u krijua, por email-i i verifikimit nuk mund të dërgohej për momentin. Ju lutemi provoni "Ridërgo email-in e verifikimit" pas pak ose na kontaktoni.',
            'generic_error' => 'Ndodhi një gabim. Ju lutemi provoni përsëri më vonë.',
            'login_invalid' => 'Email-i ose fjalëkalimi nuk është i saktë.',
            'login_rejected' => 'Kërkesa juaj për anëtarësim nuk është miratuar.',
            'login_unverified' => 'Ju lutemi verifikoni adresën tuaj të email-it para se të hyni.',

            // Email Verification & Resend
            'verify_title' => 'Verifikimi i Email-it',
            'verify_success' => 'Email-i juaj u verifikua me sukses! Tani mund të hyni në llogarinë tuaj për të parë statusin e miratimit.',
            'verify_invalid' => 'Lidhja e verifikimit është e pavlefshme ose ka skaduar. Email-i juaj mund të jetë tashmë i verifikuar, ose mund të kërkoni një lidhje të re më poshtë.',
            'verify_missing' => 'Nuk u dha asnjë kod verifikimi.',
            'go_to_login' => 'Shko te Hyrja',
            'resend_title' => 'Ridërgo Email-in e Verifikimit',
            'resend_intro' => 'Shkruani adresën e email-it me të cilën u regjistruat dhe do t’ju dërgojmë një lidhje të re verifikimi nëse llogaria është ende e paverifikuar.',
            'resend_btn' => 'Dërgo Lidhjen e Verifikimit',
            'resend_sent_generic' => 'Nëse ekziston një llogari e paverifikuar për atë adresë email-i, lidhja e re e verifikimit është dërguar.',

            // Forgot / Reset Password
            'forgot_title' => 'Rivendosni Fjalëkalimin',
            'forgot_intro' => 'Shkruani adresën e email-it të llogarisë suaj dhe do t’ju dërgojmë një lidhje të sigurt për të zgjedhur një fjalëkalim të ri.',
            'forgot_btn' => 'Dërgo Lidhjen e Rivendosjes',
            'forgot_sent_generic' => 'Nëse ekziston një llogari me atë adresë email-i, udhëzimet për rivendosjen e fjalëkalimit janë dërguar.',
            'reset_title' => 'Zgjidhni një Fjalëkalim të Ri',
            'new_password_label' => 'Fjalëkalimi i Ri',
            'confirm_password_label' => 'Konfirmo Fjalëkalimin e Ri',
            'reset_btn' => 'Ruaj Fjalëkalimin e Ri',
            'reset_invalid_token' => 'Kjo lidhje për rivendosjen e fjalëkalimit është e pavlefshme ose ka skaduar. Ju lutemi kërkoni një lidhje të re.',
            'passwords_mismatch' => 'Fjalëkalimet e reja nuk përputhen.',
            'reset_success' => 'Fjalëkalimi juaj u përditësua me sukses. Tani mund të hyni me fjalëkalimin e ri.',

            // Profile & Account Settings
            'welcome_user' => 'Mirë se vini, {username}',
            'change_username' => 'Ndrysho Emrin',
            'change_email' => 'Ndrysho Email-in',
            'change_password' => 'Ndrysho Fjalëkalimin',
            'delete_account' => 'Fshi Llogarinë',
            'account_status_label' => 'Statusi i Llogarisë:',
            'status_pending' => 'Në pritje',
            'status_approved' => 'Miratuar',
            'status_rejected' => 'Refuzuar',
            'pending_account_notice' => 'Llogaria juaj është në pritje të miratimit nga administratori. Pasi të miratohet, mund të dërgoni ide dhe të mbështesni Vlerat Themelore.',
            'my_submitted_ideas' => 'Idetë e Mia të Dërguara',
            'submit_new_idea' => 'Dërgo Ide të Re',
            'no_user_ideas' => 'Ende nuk keni dërguar asnjë ide.',
            'delete_pending_idea' => 'Tërhiq',
            'idea_deleted_flash' => 'Ideja juaj në pritje u fshi.',
            'agreed_values_heading' => 'Vlerat Themelore që Mbështes',
            'no_agreed_values' => 'Ende nuk keni mbështetur asnjë Vlerë Themelore.',
            'current_password_label' => 'Fjalëkalimi Aktual',
            'incorrect_current_password' => 'Fjalëkalimi aktual nuk është i saktë.',
            'password_changed_success' => 'Fjalëkalimi u ndryshua me sukses.',
            'new_username_label' => 'Emri i Ri i Përdoruesit',
            'username_taken' => 'Ky emër përdoruesi është i zënë.',
            'username_changed_success' => 'Emri i përdoruesit u ndryshua me sukses.',
            'current_email_label' => 'Email-i Aktual',
            'new_email_label' => 'Adresa e Re e Email-it',
            'email_taken' => 'Kjo adresë email-i është e regjistruar tashmë.',
            'email_changed_admin_success' => 'Adresa juaj e email-it u përditësua me sukses.',
            'email_changed_user_success' => 'Adresa juaj e email-it u përditësua. Ju lutemi kontrolloni kutinë postare për ta verifikuar para hyrjes së radhës.',
            'delete_account_title' => 'Fshi Llogarinë Time',
            'delete_account_warning' => 'Fshirja e llogarisë fshin përgjithmonë profilin tuaj, idetë e dërguara dhe mbështetjet për Vlerat Themelore. Ky veprim nuk mund të zhbëhet.',
            'delete_account_confirm_label' => 'Shkruani fjalëkalimin aktual për të konfirmuar fshirjen përfundimtare',
            'delete_account_btn' => 'Fshi Përgjithmonë Llogarinë Time',
            'delete_last_admin_error' => 'Nuk mund të fshini llogarinë e fundit të administratorit.',
            'account_deleted_msg' => 'Llogaria juaj dhe të dhënat tuaja personale u fshinë përgjithmonë.',

            // Contact Page
            'contact_name_label' => 'Emri dhe Mbiemri',
            'contact_email_label' => 'Adresa e Email-it',
            'contact_message_label' => 'Mesazhi',
            'contact_send_btn' => 'Dërgo Mesazhin',
            'contact_consent_html' => 'Pajtohem që AsamblejaKombetare të përdorë të dhënat e mësipërme për t’iu përgjigjur kërkesës sime sipas <a href="privacy.php" target="_blank" rel="noopener">Politikës së Privatësisë</a>.',
            'contact_success' => 'Faleminderit! Mesazhi juaj u mor me sukses dhe ekipi ynë do t’ju kontaktojë së shpejti.',
            'contact_length_error' => 'Ju lutemi shkruani një emër të vlefshëm (maks. 100 karaktere) dhe një mesazh (10–5.000 karaktere).',

            // Error Pages
            '404_title' => 'Faqja Nuk u Gjet (404)',
            '404_text' => 'Faqja që po kërkoni mund të jetë zhvendosur ose nuk ekziston më.',
            '500_title' => 'Gabim i Papritur (500)',
            '500_text' => 'Ndodhi një gabim gjatë përpunimit të kërkesës suaj. Ju lutemi provoni përsëri më vonë.',
            'back_home_btn' => 'Kthehu në Ballinë',

            // Outgoing Emails
            'email_reset_subject' => 'Rivendosja e fjalëkalimit për {site_title}',
            'email_reset_body' => "Përshëndetje {username},\n\nKemi marrë një kërkesë për të rivendosur fjalëkalimin tuaj në {site_title}.\nKlikoni lidhjen e mëposhtme brenda 60 minutave për të zgjedhur një fjalëkalim të ri:\n{reset_link}\n\nNëse nuk e keni kërkuar ju këtë ndryshim, mund ta injoroni këtë email.",
            'email_account_approved_subject' => 'Anëtarësimi juaj në {site_title} u miratua',
            'email_account_approved_body' => "Përshëndetje {username},\n\nLlogaria juaj e anëtarësimit në {site_title} është miratuar!\nTani mund të hyni për të dërguar ide dhe për të mbështetur Vlerat Themelore:\n{login_link}\n\nMe respekt,\n{site_title}",
            'email_idea_approved_subject' => 'Ideja juaj në {site_title} u publikua',
            'email_idea_approved_body' => "Përshëndetje {username},\n\nIdeja juaj \"{idea_title}\" është miratuar dhe tani është publikuar në {site_title}:\n{ideas_link}\n\nFaleminderit për kontributin tuaj!\n{site_title}",
        ],
    ];

    return $catalog;
}

function __(string $key, array $replacements = [], ?string $lang = null): string {
    $lang = $lang ?? current_lang();
    $catalog = translations_catalog();
    $text = $catalog[$lang][$key] ?? $catalog['en'][$key] ?? $key;
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', (string)$value, $text);
    }
    return $text;
}

function translate_status(string $status): string {
    switch ($status) {
        case 'approved':
            return __('status_approved');
        case 'rejected':
            return __('status_rejected');
        default:
            return __('status_pending');
    }
}
