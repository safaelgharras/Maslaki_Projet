<?php
/**
 * Google OAuth 2.0 credentials.
 *
 * Values are read exclusively from environment variables.
 * Set them in your .env file (development) or server environment (production).
 * Never hardcode credentials here.
 */

define('GOOGLE_CLIENT_ID',     getenv('GOOGLE_CLIENT_ID')     ?: '');
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET') ?: '');
define('GOOGLE_REDIRECT_URI',  getenv('GOOGLE_REDIRECT_URI')  ?: '');
?>
