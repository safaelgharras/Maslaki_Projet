/**
 * Maslaki AI Chatbot — Frontend Controller
 *
 * Handles UI interactions, AJAX communication,
 * chat history, quick actions, and typing animation.
 */

(function () {
    'use strict';

    // ── i18n strings (injected by PHP via window.__cbi18n) ─────
    const i18n = window.__cbi18n || {
        source_database: 'Database',
        source_gemini: 'Gemini AI',
        error_connection: 'Connection error. Check your network and try again.',
        error_generic: 'Sorry, an error occurred. Please try again.',
        confirm_clear: 'Delete all conversation history?',
    };

    // ── Configuration ─────────────────────────────────────────
    const CONFIG = {
        endpoint: 'chatbot.php',
        typingDelay: 600,
        maxMessages: 100,
        scrollThreshold: 50,
        context: 'profile', // overridden from data-chatbot-context
    };

    // ── State ─────────────────────────────────────────────────
    const state = {
        isOpen: false,
        isLoading: false,
        messages: [],
        historyLoaded: false,
    };

    // ── DOM References ────────────────────────────────────────
    const $ = (sel) => document.querySelector(sel);
    const $$ = (sel) => document.querySelectorAll(sel);

    let els = {};

    function cacheElements() {
        els = {
            toggle:   $('#cb-toggle'),
            window:   $('#cb-window'),
            messages: $('#cb-messages'),
            input:    $('#cb-input'),
            send:     $('#cb-send'),
            clear:    $('#cb-clear'),
            close:    $('#cb-close'),
            welcome:  $('#cb-welcome'),
            quickBtns: $$('.cb-quick-btn'),
            welcomeFeatures: $$('.cb-welcome-feature'),
        };
    }

    // ── Initialize ────────────────────────────────────────────
    function init() {
        cacheElements();

        if (!els.toggle || !els.window) return;

        // Detect base path (same logic as header.php)
        const isInViews = window.location.pathname.includes('/views/');
        CONFIG.base = isInViews ? '../' : '';

        // Read context from data attribute (set by PHP)
        const context = els.toggle.dataset.chatbotContext;
        if (context === 'orientation' || context === 'profile') {
            CONFIG.context = context;
        }

        bindEvents();
        // Note: loadHistory() is called only when the user opens the chatbot
    }

    // ── Event Binding ─────────────────────────────────────────
    function bindEvents() {
        // Toggle chat window
        els.toggle.addEventListener('click', toggleChat);
        els.close.addEventListener('click', closeChat);

        // Send message
        els.send.addEventListener('click', sendMessage);
        els.input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        // Auto-resize textarea
        els.input.addEventListener('input', () => {
            els.input.style.height = 'auto';
            els.input.style.height = Math.min(els.input.scrollHeight, 80) + 'px';
        });

        // Quick actions
        els.quickBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const question = btn.dataset.question;
                if (question) {
                    els.input.value = question;
                    sendMessage();
                }
            });
        });

        // Welcome features (clickable)
        els.welcomeFeatures.forEach(feat => {
            feat.addEventListener('click', () => {
                const question = feat.dataset.question;
                if (question) {
                    els.input.value = question;
                    sendMessage();
                }
            });
        });

        // Clear history
        els.clear.addEventListener('click', clearHistory);

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && state.isOpen) {
                closeChat();
            }
        });

        // Close on outside click (only on desktop)
        document.addEventListener('click', (e) => {
            if (state.isOpen &&
                !els.window.contains(e.target) &&
                !els.toggle.contains(e.target)) {
                // Don't close on mobile (fullscreen)
                if (window.innerWidth > 480) {
                    closeChat();
                }
            }
        });
    }

    // ── Toggle / Open / Close ─────────────────────────────────
    function toggleChat() {
        state.isOpen ? closeChat() : openChat();
    }

    function openChat() {
        state.isOpen = true;
        els.window.classList.add('active');
        els.toggle.classList.add('active');
        els.toggle.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`;
        els.input.focus();

        // Load history only once, when the chatbot is first opened
        if (!state.historyLoaded) {
            loadHistory();
        }

        scrollToBottom();
    }

    function closeChat() {
        state.isOpen = false;
        els.window.classList.remove('active');
        els.toggle.classList.remove('active');
        els.toggle.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`;
    }

    // ── Send Message ──────────────────────────────────────────
    async function sendMessage() {
        const text = els.input.value.trim();
        if (!text || state.isLoading) return;

        // Hide welcome
        if (els.welcome) {
            els.welcome.style.display = 'none';
        }

        // Add user message
        addMessage('user', text);
        els.input.value = '';
        els.input.style.height = 'auto';

        // Show typing
        showTyping();
        setLoading(true);

        try {
            const response = await fetch(CONFIG.base + CONFIG.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    question: text,
                    action: 'ask',
                    context: CONFIG.context,
                }),
            });

            const data = await response.json();

            hideTyping();

            if (data.success) {
                addMessage('bot', data.reply, data.source, data.timestamp);
            } else {
                addMessage('bot', data.message || i18n.error_generic, 'error');
            }
        } catch (err) {
            hideTyping();
            addMessage('bot', i18n.error_connection, 'error');
        }

        setLoading(false);
    }

    // ── Add Message to UI ─────────────────────────────────────
    function addMessage(role, text, source, timestamp) {
        const ts = timestamp || getCurrentTime();
        state.messages.push({ role, text, source, timestamp: ts });

        // Limit messages
        if (state.messages.length > CONFIG.maxMessages) {
            state.messages.shift();
        }

        const msgEl = document.createElement('div');
        msgEl.className = `cb-msg ${role}`;

        const avatarIcon = role === 'bot' ? '🤖' : '👤';

        let sourceTag = '';
        if (source && role === 'bot') {
            const sourceLabel = source === 'database' ? i18n.source_database :
                                source === 'gemini' ? i18n.source_gemini : '';
            if (sourceLabel) {
                sourceTag = `<span class="cb-msg-source">${escapeHtml(sourceLabel)}</span>`;
            }
        }

        const formattedText = role === 'bot' ? formatBotText(text) : escapeHtml(text);

        msgEl.innerHTML = `
            <div class="cb-msg-avatar">${avatarIcon}</div>
            <div class="cb-msg-content">
                <div class="cb-msg-bubble">${formattedText}</div>
                <div class="cb-msg-meta">
                    <span>${ts}</span>
                    ${sourceTag}
                </div>
            </div>
        `;

        els.messages.appendChild(msgEl);
        scrollToBottom();
    }

    // ── Typing Indicator ──────────────────────────────────────
    function showTyping() {
        const typing = document.createElement('div');
        typing.className = 'cb-typing';
        typing.id = 'cb-typing';
        typing.innerHTML = `
            <div class="cb-typing-avatar">🤖</div>
            <div class="cb-typing-dots">
                <span></span><span></span><span></span>
            </div>
        `;
        els.messages.appendChild(typing);
        scrollToBottom();
    }

    function hideTyping() {
        const typing = document.getElementById('cb-typing');
        if (typing) {
            typing.remove();
        }
    }

    // ── Load History ──────────────────────────────────────────
    async function loadHistory() {
        try {
            const response = await fetch(CONFIG.base + CONFIG.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'history' }),
            });

            if (!response.ok) {
                // Server error — skip history, show welcome screen
                state.historyLoaded = true;
                return;
            }

            const data = await response.json();

            if (data.success && data.messages && data.messages.length > 0) {
                // Hide welcome
                if (els.welcome) {
                    els.welcome.style.display = 'none';
                }

                data.messages.forEach(msg => {
                    addMessage('user', msg.question, null, msg.timestamp);
                    addMessage('bot', msg.answer, msg.source, msg.timestamp);
                });
            }
        } catch (err) {
            // Silently fail — welcome screen stays
        }

        state.historyLoaded = true;
    }

    // ── Clear History ─────────────────────────────────────────
    async function clearHistory() {
        if (!confirm(i18n.confirm_clear)) return;

        try {
            await fetch(CONFIG.base + CONFIG.endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'clear' }),
            });
        } catch (err) {
            // Continue anyway
        }

        // Clear UI
        els.messages.innerHTML = '';
        state.messages = [];

        // Show welcome again
        if (els.welcome) {
            els.welcome.style.display = '';
        }
    }

    // ── Loading State ─────────────────────────────────────────
    function setLoading(loading) {
        state.isLoading = loading;
        els.send.disabled = loading;
        els.input.disabled = loading;
    }

    // ── Utilities ─────────────────────────────────────────────
    function scrollToBottom() {
        requestAnimationFrame(() => {
            els.messages.scrollTop = els.messages.scrollHeight;
        });
    }

    function getCurrentTime() {
        const now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' +
               now.getMinutes().toString().padStart(2, '0');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Format bot text: convert **bold** and newlines to HTML.
     */
    function formatBotText(text) {
        if (!text) return '';

        let html = escapeHtml(text);

        // Bold: **text**
        html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Italic: *text*
        html = html.replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');

        // Bullet points: • or -
        html = html.replace(/^• (.+)$/gm, '<li>$1</li>');
        html = html.replace(/^- (.+)$/gm, '<li>$1</li>');

        // Wrap consecutive <li> in <ul>
        html = html.replace(/((?:<li>.*<\/li>\n?)+)/g, '<ul>$1</ul>');

        // Newlines → <br>
        html = html.replace(/\n/g, '<br>');

        return html;
    }

    // ── Boot ──────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
