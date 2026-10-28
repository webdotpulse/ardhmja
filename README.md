# AsamblejaKombetare — Official Website Platform

Bilingual (**Shqip `sq` / English `en`**) civic and political party platform for **AsamblejaKombetare**, ready for shared Apache / PHP-FPM hosting on **Combell**.

## Key Features
- **Bilingual (`SQ` / `EN`):** Instant language switcher in the header, localized UI/forms/emails, and bilingual CMS fields for Site Settings, Core Values, and Public Representative Profiles.
- **Member Accounts & Lifecycle:** Registration with GDPR consent, email verification, resend verification, forgot/reset password, email/username/password updates, and self-service GDPR account deletion.
- **Civic Participation:** Endorse or withdraw support for Core Values (with live supporter counts) and submit community ideas (with moderation & withdrawal of pending ideas).
- **Email Notifications (PHPMailer SMTP):** Verification & password-reset emails, user notifications when accounts or ideas are approved, and admin alerts on new registrations, submitted ideas, and contact inquiries.
- **Hardened Security:** Synchronizer CSRF tokens across all forms, strict `Content-Security-Policy` nonces, `DOMDocument` HTML allowlist sanitization, live session role/status verification, hashed-IP rate limiting + honeypots, self-hosted fonts (`Inter` & `Montserrat`), and Apache `.htaccess` protection (`uploads/`, `includes/`, `vendor/`, `.sql`, `.git`).

## Deployment & Upgrading Existing Databases
See `install.txt` for step-by-step Combell instructions—covering **both upgrading an existing database without losing any users or content** and **fresh installations**.
