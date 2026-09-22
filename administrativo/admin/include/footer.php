<footer class="footer admin-footer" role="contentinfo">
  <div class="admin-footer-inner">
    <div class="admin-footer-brand">
      <img
        src="assets/images/darkenergy.png"
        alt="Dark Energy Software"
        class="admin-footer-logo"
        width="36"
        height="36"
        loading="lazy"
        decoding="async">
      <div class="admin-footer-copy">
        <span class="admin-footer-name">Dark Energy Solutions</span>
        <span class="admin-footer-meta">Estadística360 · Software</span>
      </div>
    </div>

    <div class="admin-footer-right">
      <span class="admin-footer-year">&copy; <?= date('Y') ?></span>
      <span class="admin-footer-sep" aria-hidden="true"></span>
      <span class="admin-footer-version">v 5.0</span>
    </div>
  </div>
</footer>

<style>
  /* ==========================================================
     Footer administrativo — barra compacta (línea gráfica navy)
     ========================================================== */
  /* El tema reserva padding-bottom para footer absolute; aquí el footer es flujo normal */
  .content:has(> .footer.admin-footer),
  .content:has(> footer.admin-footer) {
    padding-bottom: 0 !important;
  }

  .content .footer.admin-footer,
  footer.footer.admin-footer {
    position: relative;
    left: auto;
    right: auto;
    bottom: auto;
    z-index: 10;
    width: 100%;
    height: auto !important;
    min-height: 0;
    margin: 0;
    padding: 0.55rem 1.25rem;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    background:
      radial-gradient(420px 80px at 8% 0%, rgba(75, 140, 247, 0.22), transparent 70%),
      linear-gradient(135deg, #0A2248 0%, #132b52 48%, #07162E 100%);
    box-shadow: 0 -8px 28px rgba(8, 28, 63, 0.12);
    color: rgba(255, 255, 255, 0.88);
  }

  .admin-footer-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px 20px;
    width: 100%;
    max-width: 100%;
    min-height: 2.15rem;
  }

  .admin-footer-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
  }

  .admin-footer-logo {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    object-fit: cover;
    object-position: center 12%;
    border-radius: 9px;
    background: #000;
    box-shadow:
      inset 0 0 0 1px rgba(255, 255, 255, 0.12),
      0 4px 12px rgba(0, 0, 0, 0.28);
  }

  .admin-footer-copy {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
  }

  .admin-footer-name {
    color: #fff;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .admin-footer-meta {
    color: rgba(183, 208, 255, 0.78);
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    line-height: 1.2;
  }

  .admin-footer-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 0 0 auto;
    color: rgba(255, 255, 255, 0.62);
    font-size: 0.72rem;
    font-weight: 650;
  }

  .admin-footer-sep {
    width: 1px;
    height: 12px;
    background: rgba(255, 255, 255, 0.22);
  }

  .admin-footer-version {
    color: #B7D0FF;
    font-variant-numeric: tabular-nums;
  }

  @media (min-width: 576px) {
    .content .footer.admin-footer,
    footer.footer.admin-footer {
      padding: 0.6rem 1.5rem;
    }
  }

  @media (min-width: 992px) {
    .content .footer.admin-footer,
    footer.footer.admin-footer {
      padding: 0.6rem 2.5rem;
    }
  }

  @media (max-width: 480px) {
    .admin-footer-inner {
      flex-wrap: wrap;
      justify-content: center;
      text-align: center;
    }

    .admin-footer-brand {
      justify-content: center;
      width: 100%;
    }

    .admin-footer-copy {
      align-items: flex-start;
      text-align: left;
    }

    .admin-footer-right {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<?php
// Widget n8n "Yamil" desconectado — reemplazado por Asistente IA propio.
// Ver docs/FASE-E-PLAN-ASISTENTE-IA.md
?>
