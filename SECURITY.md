# Security Policy

## Reporting a vulnerability

Report vulnerabilities privately to the repository owner through an established private contact channel. If GitHub private vulnerability reporting is enabled, use the repository's **Security** tab. Do not open a public issue containing an exploit, credential, personal record, or private document.

Include the affected commit, component, expected behavior, and minimal reproduction steps using synthetic data. Redact tokens, passwords, employee details, and infrastructure addresses. No response-time or support guarantee is implied.

## Deployment requirements

- Keep secrets in environment variables or a dedicated secret store; never commit real `.env` files.
- Use HTTPS, disable debug mode, and set secure, HTTP-only session cookies.
- Disable public registration for production internal deployments unless intentionally approved.
- Provision administrators through `php artisan app:create-administrator`; never use shared or sample passwords.
- Serve the `public` directory only. Protect private storage, database backups, logs, and exports.
- Use least-privilege database and service accounts. Review application roles and repository access regularly.
- Restrict demo seeders and tests to disposable databases.
- Audit Composer and npm dependencies regularly and review updates before deployment.

## Repository hygiene

Source snapshots must exclude credentials, private keys, database dumps, uploaded user files, session data, caches, and build/runtime dependencies. Inspect staged files before every push. If a secret has already been published, revoke or rotate it immediately; deleting the current file does not remove it from history.

GitHub secret scanning, push protection, dependency alerts, and branch protection should be enabled where available. Repository administrators must configure and review these controls; this policy does not imply they are already enabled.
