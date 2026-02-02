Phase 3 - Remediation and Patches
TechNovation Solutions – Secure Web Portal (Phase 3)
GitHub Repository: https://github.com/galohsafiya/Ethical-Hacking-Remediated.git

Database Migration: Import the provided secure SQL schema into your MySQL instance.

Firewall Setup: Enable UFW and apply the documented ruleset (sudo ufw allow 80,443/tcp).

Verification: Conduct a follow-up scan to verify that all previously identified vulnerabilities are successfully remediated.

Project Overview
This repository contains the hardened source code and configuration files for the TechNovation Solutions e-commerce portal. Following the vulnerabilities identified in Phase 2, this phase implements comprehensive security remediations to ensure the integrity, confidentiality, and availability of the system and its data.

Security Remediations (Phase 3)
The following technical controls have been implemented to address the OWASP Top 10 vulnerabilities identified during the penetration testing phase:

1. SQL Injection (SQLi) Mitigation
Fix: Replaced all legacy mysqli_query string concatenations with PDO (PHP Data Objects) Prepared Statements.

Outcome: Effectively separates SQL logic from user-supplied data, neutralizing the risk of authentication bypass and unauthorized data extraction.

2. Broken Authentication & Data Protection
Fix: Migrated from plaintext storage to Bcrypt hashing using password_hash() and password_verify().

Outcome: Ensures secure Data at Rest by preventing password recovery from database leaks.

3. Cross-Site Scripting (XSS) Mitigation
Fix: Applied Output Encoding using the htmlspecialchars() function across all user-facing scripts.

Outcome: Prevents malicious scripts from being executed in the user's browser context.

4. OS & Infrastructure Hardening
Fix: Removed the SUID bit from vulnerable binaries and implemented strict file permissions.

Fix: Configured UFW (Uncomplicated Firewall) to restrict the attack surface by closing unnecessary ports.

Fix: Enforced TLS 1.3 for secure Data in Transit.

Deployment Instructions
1. Clone the Repository: git clone https://github.com/galohsafiya/Ethical-Hacking-Remediated.git
2. Follow the Remediation Steps provided in the document
3. Verification: Conduct a follow-up scan to verify that all previously identified vulnerabilities are successfully remediated.

