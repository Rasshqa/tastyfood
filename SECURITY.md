# Security Policy & Secret Audit Report

## Audit Findings & Mitigations (TastyFood Monorepo)

1. **Git Remote Token Leak (Resolved)**:
   - Previous isolated `TastyFood` repository had a GitHub Personal Access Token (`ghp_...`) embedded in `.git/config` remote URL (`https://Rasshqa:ghp_...@github.com/Rasshqa/Padang-Rice.git`).
   - Mitigated: The unified repository uses authenticated Git credential helpers via `gh` with zero hardcoded credentials in `.git/config`.
   - *Recommendation*: Revoke the old `ghp_...` token from GitHub Developer Settings -> Personal access tokens.

2. **Database SQL Dump (Sanitized & Untracked)**:
   - File `tastyfood_complete.sql` contained raw password hashes (`$2y$12$...`) and user emails.
   - Mitigated: Added `*.sql` to `.gitignore`. Database setup is reproducible purely through Laravel migrations (`php artisan migrate`) and seeders (`DatabaseSeeder`).

3. **Environment Variables**:
   - `.env` is ignored by `.gitignore`.
   - `.env.example` provides complete template variables (including Laravel Reverb broadcast credentials) without pre-filled secrets.

4. **API Authentication**:
   - Session & CSRF guards configured to protect web routes while allowing authenticated API interactions for mobile clients.
