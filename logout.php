<?php
session_start();

/* * REMEDIATION: SESSION MANAGEMENT HARDENING
 * To prevent "Back-button Re-entry" identified in Phase 2, 
 * we must clear the $_SESSION array entirely before destruction.
 */
$_SESSION = array();

/* * REMEDIATION: CLIENT-SIDE TOKEN CLEARING
 * If session cookies are used, we expire the cookie manually to ensure 
 * the browser discards the session ID.
 */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

/* * REMEDIATION: SERVER-SIDE DESTRUCTION
 * Finalizes the Hard session destruction logic listed in the 
 * Phase 3 Remediation table.
 */
session_destroy();

header("Location: index.php?msg=logged_out");
exit;
