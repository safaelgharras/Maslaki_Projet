<?php
/**
 * Chatbot Widget — Conditional include (only on allowed pages).
 *
 * Expected variables (set in footer.php):
 * - $chatbotContext: 'profile' | 'orientation'
 * - $base: path prefix from header.php
 */

// Default context if not set
if (!isset($chatbotContext)) {
    $chatbotContext = 'profile';
}

// ── Context-specific configuration using __() for i18n ──────────
$chatbotConfigs = [
    'profile' => [
        'headerTitle'  => __('cb_header_title_profile'),
        'welcomeIcon'  => '👋',
        'welcomeTitle' => __('cb_welcome_profile'),
        'welcomeSub'   => __('cb_welcome_sub_profile'),
        'features'     => [
            ['icon' => '🎓', 'label' => __('cb_feat_schools_city'),       'question' => __('cb_feat_schools_city')],
            ['icon' => '📊', 'label' => __('cb_feat_schools_avg'),       'question' => __('cb_feat_schools_avg')],
            ['icon' => '⭐', 'label' => __('cb_feat_saved'),             'question' => __('cb_feat_saved')],
            ['icon' => '💡', 'label' => __('cb_feat_recommendations'),   'question' => __('cb_feat_recommendations')],
        ],
        'quickActions' => [
            ['icon' => '🎓', 'label' => __('cb_quick_city'),    'question' => __('cb_feat_schools_city')],
            ['icon' => '📊', 'label' => __('cb_quick_avg'),     'question' => __('cb_feat_schools_avg')],
            ['icon' => '⭐', 'label' => __('cb_quick_saved'),   'question' => __('cb_feat_saved')],
            ['icon' => '💡', 'label' => __('cb_quick_reco'),    'question' => __('cb_feat_recommendations')],
        ],
        'placeholder' => __('cb_placeholder_profile'),
    ],
    'orientation' => [
        'headerTitle'  => __('cb_header_title_orientation'),
        'welcomeIcon'  => '🎓',
        'welcomeTitle' => __('cb_welcome_orientation'),
        'welcomeSub'   => __('cb_welcome_sub_orientation'),
        'features'     => [
            ['icon' => '🏫', 'label' => __('cb_feat_analyze_profile'),    'question' => __('cb_feat_analyze_profile')],
            ['icon' => '📊', 'label' => __('cb_feat_compare'),           'question' => __('cb_feat_compare')],
            ['icon' => '📅', 'label' => __('cb_feat_deadlines'),         'question' => __('cb_feat_deadlines')],
            ['icon' => '🤖', 'label' => __('cb_feat_orientation_advice'), 'question' => __('cb_feat_orientation_advice')],
        ],
        'quickActions' => [
            ['icon' => '🏫', 'label' => __('cb_quick_profile'),   'question' => __('cb_feat_analyze_profile')],
            ['icon' => '📊', 'label' => __('cb_quick_compare'),   'question' => __('cb_feat_compare')],
            ['icon' => '📅', 'label' => __('cb_quick_deadlines'), 'question' => __('cb_feat_deadlines')],
            ['icon' => '🤖', 'label' => __('cb_quick_advice'),    'question' => __('cb_feat_orientation_advice')],
        ],
        'placeholder' => __('cb_placeholder_orientation'),
    ],
];

$cb = $chatbotConfigs[$chatbotContext] ?? $chatbotConfigs['profile'];

// ── i18n strings for JavaScript (passed as data attributes) ─────
$cbJsStrings = [
    'online'          => __('cb_online'),
    'source_database' => __('cb_source_database'),
    'source_gemini'   => __('cb_source_gemini'),
    'error_connection' => __('cb_error_connection'),
    'error_generic'   => __('cb_error_generic'),
    'confirm_clear'   => __('cb_confirm_clear'),
];
?>
<!-- Maslaki AI Chatbot (context: <?php echo $chatbotContext; ?>) -->
<link rel="stylesheet" href="<?php echo $base; ?>assets/css/chatbot.css">

<!-- Toggle Button -->
<button class="cb-toggle" id="cb-toggle" aria-label="<?php echo __('cb_toggle_label'); ?>"
        data-chatbot-context="<?php echo htmlspecialchars($chatbotContext); ?>">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
</button>

<!-- Chat Window -->
<div class="cb-window" id="cb-window">
    <!-- Header -->
    <div class="cb-header">
        <div class="cb-header-avatar">🤖</div>
        <div class="cb-header-info">
            <div class="cb-header-title"><?php echo $cb['headerTitle']; ?></div>
            <div class="cb-header-status">
                <span class="cb-status-dot"></span>
                <?php echo __('cb_online'); ?>
            </div>
        </div>
        <div class="cb-header-actions">
            <button class="cb-header-btn" id="cb-clear" title="<?php echo __('cb_clear_history'); ?>" aria-label="<?php echo __('cb_clear_history'); ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
            </button>
            <button class="cb-header-btn" id="cb-close" title="<?php echo __('cb_close'); ?>" aria-label="<?php echo __('cb_close'); ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Messages Area -->
    <div class="cb-messages" id="cb-messages">
        <!-- Welcome Screen -->
        <div class="cb-welcome" id="cb-welcome">
            <span class="cb-welcome-icon"><?php echo $cb['welcomeIcon']; ?></span>
            <div class="cb-welcome-title"><?php echo $cb['welcomeTitle']; ?></div>
            <div class="cb-welcome-subtitle">
                <?php echo $cb['welcomeSub']; ?>
            </div>
            <div class="cb-welcome-features">
                <?php foreach ($cb['features'] as $feat): ?>
                <div class="cb-welcome-feature" data-question="<?php echo htmlspecialchars($feat['question']); ?>">
                    <span><?php echo $feat['icon']; ?></span> <?php echo htmlspecialchars($feat['label']); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="cb-quick-actions">
        <?php foreach ($cb['quickActions'] as $qa): ?>
        <button class="cb-quick-btn" data-question="<?php echo htmlspecialchars($qa['question']); ?>">
            <?php echo $qa['icon']; ?> <?php echo htmlspecialchars($qa['label']); ?>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- Input Area -->
    <div class="cb-input-area">
        <textarea class="cb-input" id="cb-input" placeholder="<?php echo htmlspecialchars($cb['placeholder']); ?>" rows="1" maxlength="1000"></textarea>
        <button class="cb-send" id="cb-send" aria-label="<?php echo __('cb_send', 'Envoyer'); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
        </button>
    </div>
</div>

<!-- Pass i18n strings to JavaScript -->
<script>
window.__cbi18n = <?php echo json_encode($cbJsStrings, JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="<?php echo $base; ?>assets/js/chatbot.js"></script>