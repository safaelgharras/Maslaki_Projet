<?php
/**
 * Gemini API Test Page — Diagnostics & Live Test
 *
 * Shows: API status, SSL status, HTTP code, raw response, parsed response, final AI answer.
 */

require_once __DIR__ . '/services/GeminiService.php';

$gemini = new GeminiService();
$diag   = $gemini->diagnostics();

$testResult = null;
$testQuestion = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['question'])) {
    $testQuestion = trim($_POST['question']);
    $testResult = $gemini->ask($testQuestion);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Gemini API — Maslaki</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            padding: 40px 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
            color: #C5AD59;
        }
        .subtitle {
            color: #94a3b8;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        .card {
            background: #1e293b;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid #334155;
        }
        .card h2 {
            font-size: 1.1rem;
            color: #C5AD59;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }
        .status-item {
            background: #0f172a;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid #334155;
        }
        .status-item .label {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .status-item .value {
            font-weight: 700;
            font-size: 0.95rem;
        }
        .value.ok { color: #22c55e; }
        .value.err { color: #ef4444; }
        .value.warn { color: #f59e0b; }
        .value.info { color: #60a5fa; }
        .test-form {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        .test-form input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #334155;
            border-radius: 10px;
            background: #0f172a;
            color: #e2e8f0;
            font-size: 0.95rem;
            outline: none;
        }
        .test-form input:focus {
            border-color: #C5AD59;
        }
        .test-form button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #C5AD59, #a8903a);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .test-form button:hover { opacity: 0.9; }
        pre {
            background: #0f172a;
            padding: 16px;
            border-radius: 10px;
            overflow-x: auto;
            font-size: 0.82rem;
            line-height: 1.5;
            border: 1px solid #334155;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .result-section { margin-top: 16px; }
        .result-section h3 {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .answer-box {
            background: #0f172a;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #C5AD59;
            font-size: 1rem;
            line-height: 1.7;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-success { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
        .badge-error { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Gemini API</h1>
        <p class="subtitle">Diagnostic et test en direct de l'intégration Google Gemini 2.5 Flash</p>

        <!-- Diagnostics -->
        <div class="card">
            <h2>📊 État du Service</h2>
            <div class="status-grid">
                <div class="status-item">
                    <div class="label">Clé API</div>
                    <div class="value <?= $diag['api_key_set'] ? 'ok' : 'err'; ?>">
                        <?= $diag['api_key_set'] ? '✓ Configurée' : '✗ Manquante'; ?>
                    </div>
                    <div style="font-size:0.8rem; color:#64748b; margin-top:2px;"><?= htmlspecialchars($diag['api_key_preview']); ?></div>
                </div>
                <div class="status-item">
                    <div class="label">Modèles (priorité)</div>
                    <div class="value info"><?= htmlspecialchars(implode(' → ', $diag['models'])); ?></div>
                </div>
                <div class="status-item">
                    <div class="label">Tentatives / modèle</div>
                    <div class="value info"><?= $diag['max_retries']; ?> (délai: <?= $diag['retry_delay']; ?>s)</div>
                </div>
                <div class="status-item">
                    <div class="label">cURL</div>
                    <div class="value <?= $diag['curl_available'] ? 'ok' : 'err'; ?>">
                        <?= $diag['curl_available'] ? '✓ Disponible' : '✗ Indisponible'; ?>
                    </div>
                </div>
                <div class="status-item">
                    <div class="label">OpenSSL</div>
                    <div class="value <?= $diag['openssl_available'] ? 'ok' : 'err'; ?>">
                        <?= $diag['openssl_available'] ? '✓ Disponible' : '✗ Indisponible'; ?>
                    </div>
                </div>
                <div class="status-item">
                    <div class="label">SSL Debug</div>
                    <div class="value <?= $diag['ssl_debug'] ? 'warn' : 'info'; ?>">
                        <?= $diag['ssl_debug'] ? '⚠ Activé (dev only)' : 'Désactivé (prod)'; ?>
                    </div>
                </div>
                <div class="status-item">
                    <div class="label">Certificat SSL</div>
                    <div class="value <?= $diag['ssl_cert_path'] ? ($diag['ssl_cert_exists'] ? 'ok' : 'err') : 'warn'; ?>">
                        <?= $diag['ssl_cert_path']
                            ? ($diag['ssl_cert_exists'] ? '✓ Trouvé' : '✗ Introuvable')
                            : '⚠ Non détecté'; ?>
                    </div>
                    <?php if ($diag['ssl_cert_path']): ?>
                        <div style="font-size:0.75rem; color:#64748b; margin-top:2px; word-break:break-all;">
                            <?= htmlspecialchars($diag['ssl_cert_path']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Live Test -->
        <div class="card">
            <h2>🚀 Test en Direct</h2>
            <form method="POST" class="test-form">
                <input type="text" name="question" placeholder="Posez une question à Gemini..." value="<?= htmlspecialchars($testQuestion); ?>" required>
                <button type="submit">Envoyer</button>
            </form>

            <?php if ($testResult !== null): ?>
                <div class="result-section">
                    <h3>Statut HTTP</h3>
                    <span class="badge <?= $testResult['success'] ? 'badge-success' : 'badge-error'; ?>">
                        HTTP <?= $testResult['http_code'] ?? 'N/A'; ?>
                    </span>
                </div>

                <?php if ($testResult['error']): ?>
                    <div class="result-section">
                        <h3>Erreur</h3>
                        <pre style="color: #ef4444;"><?= htmlspecialchars($testResult['error']); ?></pre>
                    </div>
                <?php endif; ?>

                <?php if ($testResult['raw']): ?>
                    <div class="result-section">
                        <h3>Réponse Brute (JSON)</h3>
                        <pre><?= htmlspecialchars(json_encode($testResult['raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                    </div>
                <?php endif; ?>

                <?php if ($testResult['reply']): ?>
                    <div class="result-section">
                        <h3>Réponse IA</h3>
                        <div class="answer-box"><?= nl2br(htmlspecialchars($testResult['reply'])); ?></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Installation Guide -->
        <div class="card">
            <h2>📋 Instructions d'Installation</h2>
            <pre>
1. Ajoutez votre clé API dans le fichier .env :
   GEMINI_API_KEY=votre_cle_ici

2. Exécutez le script SQL pour créer la table chatbot_logs :
   database/chatbot_logs.sql

3. En cas de problème SSL en local, ajoutez dans .env :
   GEMINI_DEBUG_SSL=true
   (Ne jamais utiliser en production !)

4. Le chatbot est automatiquement intégré via includes/chatbot.php
   dans le footer du site.

5. Testez avec cette page pour vérifier la connexion API.
</pre>
        </div>
    </div>
</body>
</html>
