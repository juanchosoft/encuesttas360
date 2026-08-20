<footer class="footer custom-footer">
  <div class="row g-0 justify-content-between align-items-center h-100">
    <div class="col-12 col-sm-auto text-center">
      <p class="mb-0 mt-2 mt-sm-0 text-body d-flex align-items-center justify-content-center gap-2 flex-wrap">
        <img src="assets/images/darkenergy.png" alt="Dark Energy Logo" class="footer-logo">
        <span class="footer-divider">|</span>
        <span class="footer-brand">Dark Energy Solutions</span>
      </p>
    </div>

    <div class="col-12 col-sm-auto text-center">
      <p class="mb-0 text-body-tertiary text-opacity-85 footer-version">v 5.0</p>
    </div>
  </div>
</footer>

<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />

<style>
  .custom-footer {
    position: relative !important;
    width: 100%;
    padding: 18px 20px;
    background: #f5f6f8;
    border-top: 1px solid #d9dde3;
    z-index: 10;
  }

  .footer-logo {
    height: 55px;
    max-width: 100%;
    object-fit: contain;
  }

  .footer-divider {
    font-weight: 600;
    color: #6b7280;
  }

  .footer-brand {
    font-weight: 600;
    color: #2b2f36;
    font-size: 15px;
  }

  .footer-version {
    font-size: 14px;
    color: #6b7280;
  }

  :root {
    --n8n-chat-primary-color: #343a40;
    --n8n-chat-primary-shade-50: #2b3137;
    --n8n-chat-primary-shade-100: #21262c;
    --n8n-chat-secondary-color: #eef1f4;
    --n8n-chat-background-color: #f7f8fa;
    --n8n-chat-text-color: #1f2937;
    --n8n-chat-border-radius: 20px;
    --n8n-chat-window-width: 380px;
    --n8n-chat-window-height: 620px;
  }

  /* Ventana del chat */
  .n8n-chat-window,
  .n8n-chat__window,
  [class*="chat-window"] {
    z-index: 99999 !important;
    border-radius: 20px !important;
    overflow: hidden !important;
    box-shadow: 0 18px 50px rgba(31, 41, 55, 0.18) !important;
    border: 1px solid rgba(43, 47, 54, 0.10) !important;
    bottom: 112px !important;
    right: 20px !important;
    background: #f7f8fa !important;
  }

  .yamil-chat-window-hidden {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
  }

  /* Header */
  .n8n-chat-header,
  .n8n-chat__header,
  [class*="chat-header"] {
    background: linear-gradient(135deg, #2d333b 0%, #404853 100%) !important;
    color: #fff !important;
    border-bottom: none !important;
  }

  .n8n-chat-header *,
  .n8n-chat__header * {
    color: #fff !important;
  }

  /* Body */
  .n8n-chat-body,
  .n8n-chat__body,
  [class*="chat-body"] {
    background: linear-gradient(180deg, #f7f8fa 0%, #eceff3 100%) !important;
  }

  /* Mensajes bot */
  .n8n-chat-message-from-bot,
  .n8n-chat-message--incoming,
  .n8n-chat__message--incoming,
  [class*="incoming"] {
    background: #ffffff !important;
    color: #1f2937 !important;
    border: 1px solid #e3e7ed !important;
    border-radius: 15px !important;
    box-shadow: 0 6px 14px rgba(31, 41, 55, 0.05) !important;
  }

  /* Mensajes usuario */
  .n8n-chat-message-from-user,
  .n8n-chat-message--outgoing,
  .n8n-chat__message--outgoing,
  [class*="outgoing"] {
    background: linear-gradient(135deg, #2f3640 0%, #4a5561 100%) !important;
    color: #ffffff !important;
    border-radius: 15px !important;
    box-shadow: 0 8px 18px rgba(31, 41, 55, 0.10) !important;
  }

  /* Input */
  .n8n-chat-input-area,
  .n8n-chat-footer,
  .n8n-chat__footer,
  [class*="chat-input"] {
    background: #ffffff !important;
    border-top: 1px solid #e5e7eb !important;
  }

  .n8n-chat-input,
  .n8n-chat textarea,
  .n8n-chat input,
  .n8n-chat__input,
  .n8n-chat__textarea {
    border-radius: 14px !important;
    border: 1px solid #d7dde5 !important;
    background: #f8fafc !important;
    color: #1f2937 !important;
  }

  .n8n-chat-input:focus,
  .n8n-chat textarea:focus,
  .n8n-chat input:focus,
  .n8n-chat__input:focus,
  .n8n-chat__textarea:focus {
    border-color: #4b5563 !important;
    box-shadow: 0 0 0 4px rgba(75, 85, 99, 0.10) !important;
    outline: none !important;
  }

  /* Ocultar launcher nativo y wrappers */
  .n8n-chat-launcher,
  .n8n-chat-button,
  .n8n-chat-toggle,
  [class*="chat-launcher"],
  [class*="chat-toggle"],
  [class*="chat-button"],
  [class*="launcher"] {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
  }

  .yamil-force-hide-launcher {
    display: none !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
  }

  /* Label más fino */
  .yamil-chat-label {
    position: fixed;
    right: 96px;
    bottom: 48px;
    z-index: 100000;
    background: rgba(52, 58, 64, 0.94);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .2px;
    padding: 8px 12px;
    border-radius: 999px;
    box-shadow: 0 8px 18px rgba(31, 41, 55, 0.14);
    user-select: none;
    backdrop-filter: blur(6px);
    transition: all .25s ease;
    white-space: nowrap;
  }

  .yamil-chat-label::after {
    content: '';
    position: absolute;
    right: -5px;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
    width: 10px;
    height: 10px;
    background: rgba(73, 80, 87, 0.96);
  }

  .yamil-chat-label.hidden {
    opacity: 0;
    visibility: hidden;
    transform: translateX(8px);
  }

  /* Botón Yamil más elegante */
  .yamil-chat-trigger {
    position: fixed;
    right: 18px;
    bottom: 18px;
    width: 78px;
    height: 78px;
    z-index: 100001;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .25s ease, box-shadow .25s ease;
    background: linear-gradient(135deg, #323841 0%, #59616c 100%);
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid rgba(255, 255, 255, 0.95);
    box-shadow:
      0 10px 24px rgba(31, 41, 55, 0.22),
      0 0 0 5px rgba(75, 85, 99, 0.08);
    animation: yamilFloat 3.2s ease-in-out infinite;
  }

  .yamil-chat-trigger:hover {
    transform: scale(1.05);
    box-shadow:
      0 14px 28px rgba(31, 41, 55, 0.26),
      0 0 0 6px rgba(75, 85, 99, 0.10);
  }

  .yamil-chat-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border-radius: 50%;
    background: transparent;
    pointer-events: none;
  }

  .yamil-chat-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    pointer-events: none;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.16);
  }

  @keyframes yamilFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
  }

  @media (max-width: 768px) {
    .custom-footer {
      padding: 15px 12px;
    }

    .footer-logo {
      height: 45px;
    }

    .footer-brand,
    .footer-version {
      font-size: 13px;
    }

    .n8n-chat-window,
    .n8n-chat__window,
    [class*="chat-window"] {
      left: 10px !important;
      right: 10px !important;
      bottom: 92px !important;
      width: auto !important;
      max-width: unset !important;
      height: 70vh !important;
      border-radius: 18px !important;
    }

    .yamil-chat-trigger {
      width: 70px;
      height: 70px;
      right: 12px;
      bottom: 14px;
    }

    .yamil-chat-label {
      right: 88px;
      bottom: 36px;
      font-size: 11px;
      padding: 7px 10px;
    }
  }

  @media (max-width: 480px) {
    .yamil-chat-label {
      display: none;
    }
  }
</style>

<div class="yamil-chat-label" id="yamilChatLabel">Habla con Yamil</div>

<div class="yamil-chat-trigger" id="yamilChatTrigger" title="Hablar con Yamil">
  <video class="yamil-chat-video" autoplay muted loop playsinline preload="auto">
    <source src="assets/img/yamil.mp4" type="video/mp4">
    Tu navegador no soporta video HTML5.
  </video>
  <span class="yamil-chat-ring"></span>
</div>

<script type="module">
  import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

  let yamilChatReady = false;
  let yamilChatWasHiddenOnce = false;

  createChat({
    webhookUrl: 'https://n8n.srv999835.hstgr.cloud/webhook/2c241de4-5fd4-4178-b64f-ae401b5fa2b9/chat',
    mode: 'window',
    showWelcomeScreen: false,
    loadPreviousSession: true,
    chatInputKey: 'chatInput',
    chatSessionKey: 'sessionId',
    defaultLanguage: 'es',
    initialMessages: [
      '¡Hola! 👋',
      'Soy Yamil, tu asistente virtual. ¿En qué puedo ayudarte hoy?'
    ],
    i18n: {
      es: {
        title: 'Yamil',
        subtitle: 'Asistente virtual',
        footer: '',
        getStarted: 'Nueva conversación',
        inputPlaceholder: 'Escribe tu mensaje...'
      }
    },
    enableStreaming: false
  });

  function getChatWindow() {
    return document.querySelector('.n8n-chat-window')
      || document.querySelector('.n8n-chat__window')
      || document.querySelector('[class*="chat-window"]');
  }

  function getLabel() {
    return document.getElementById('yamilChatLabel');
  }

  function hideElement(el) {
    if (!el) return;
    el.classList.add('yamil-force-hide-launcher');
  }

  function hideNativeLauncher() {
    const selectors = [
      '.n8n-chat-launcher',
      '.n8n-chat-button',
      '.n8n-chat-toggle',
      '[class*="chat-launcher"]',
      '[class*="chat-toggle"]',
      '[class*="chat-button"]',
      '[class*="launcher"]'
    ];

    selectors.forEach(selector => {
      document.querySelectorAll(selector).forEach(el => {
        if (el.id === 'yamilChatTrigger') return;
        if (el.closest('.n8n-chat-window') || el.closest('.n8n-chat__window')) return;

        hideElement(el);

        let parent = el.parentElement;
        let levels = 0;

        while (parent && levels < 4) {
          if (parent.id === 'yamilChatTrigger') break;
          if (parent.closest('.n8n-chat-window') || parent.closest('.n8n-chat__window')) break;

          const style = window.getComputedStyle(parent);
          const rect = parent.getBoundingClientRect();

          const isFloatingShell =
            (style.position === 'fixed' || style.position === 'absolute') &&
            rect.width <= 140 &&
            rect.height <= 140;

          if (isFloatingShell) {
            hideElement(parent);
          }

          parent = parent.parentElement;
          levels++;
        }
      });
    });
  }

  function hideChatInitially() {
    const chat = getChatWindow();
    if (!chat || yamilChatWasHiddenOnce) return;

    chat.classList.add('yamil-chat-window-hidden');
    yamilChatWasHiddenOnce = true;
    yamilChatReady = true;
  }

  function syncLabel() {
    const chat = getChatWindow();
    const label = getLabel();

    if (!chat || !label) return;

    const visible = !chat.classList.contains('yamil-chat-window-hidden');

    if (visible) {
      label.classList.add('hidden');
    } else {
      label.classList.remove('hidden');
    }
  }

  function openChat() {
    const chat = getChatWindow();
    if (!chat) return;

    chat.classList.remove('yamil-chat-window-hidden');
    syncLabel();
  }

  function closeChat() {
    const chat = getChatWindow();
    if (!chat) return;

    chat.classList.add('yamil-chat-window-hidden');
    syncLabel();
  }

  function toggleChat() {
    const chat = getChatWindow();
    if (!chat) return;

    if (chat.classList.contains('yamil-chat-window-hidden')) {
      openChat();
    } else {
      closeChat();
    }
  }

  function bindTrigger() {
    const trigger = document.getElementById('yamilChatTrigger');
    if (!trigger || trigger.dataset.bound === '1') return;

    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      toggleChat();
    });

    trigger.dataset.bound = '1';
  }

  function initYamilChat() {
    hideNativeLauncher();
    bindTrigger();
    hideChatInitially();
    syncLabel();
  }

  window.addEventListener('load', function() {
    let tries = 0;

    const timer = setInterval(() => {
      tries++;
      initYamilChat();

      if (yamilChatReady || tries >= 20) {
        clearInterval(timer);
      }
    }, 400);

    setTimeout(hideNativeLauncher, 1200);
    setTimeout(hideNativeLauncher, 2200);
  });
</script>