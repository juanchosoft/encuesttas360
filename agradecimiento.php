<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/admin/classes/Util.php';

// Fase B: no exige sesión de cuenta; basta haber completado una participación
$graciasOk = !empty($_SESSION['participacion_gracias']['ts'])
    && (time() - (int)$_SESSION['participacion_gracias']['ts'] < 3600);

$logo360 = 'assets/img/360 Estadisticas-04.png';

function e360($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gracias por participar | 360 Estadísticas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root{ --brand:#13357b; --brand2:#0b2a63; --ink:#0f172a; --muted:#64748b; --page:#f4f7fc; }
    body{
      font-family: "Nunito Sans", system-ui, sans-serif;
      background:
        radial-gradient(900px 320px at 10% -10%, rgba(19,53,123,.16), transparent 55%),
        radial-gradient(700px 280px at 100% 0%, rgba(11,42,99,.10), transparent 50%),
        var(--page);
      color: var(--ink);
      min-height: 100vh;
    }
    .thanks-wrap{ max-width:720px; margin:0 auto; padding:28px 16px 48px; }
    .thanks-card{
      background:#fff; border:1px solid rgba(2,6,23,.08); border-radius:24px;
      box-shadow:0 18px 50px rgba(2,6,23,.10); padding:28px 22px; text-align:center;
    }
    .thanks-logo{ width:min(220px,70vw); height:auto; margin-bottom:18px; }
    .thanks-icon{
      width:72px; height:72px; margin:0 auto 16px; border-radius:50%;
      display:flex; align-items:center; justify-content:center;
      background:linear-gradient(135deg,var(--brand),var(--brand2)); color:#fff; font-size:1.8rem;
      box-shadow:0 14px 30px rgba(19,53,123,.28);
    }
    .thanks-card h1{ font-weight:900; font-size:clamp(1.35rem,4vw,1.9rem); margin:0 0 10px; letter-spacing:-.02em; }
    .thanks-card p{ color:var(--muted); margin:0 auto 22px; max-width:34rem; line-height:1.45; }
    .thanks-actions{ display:flex; flex-wrap:wrap; gap:10px; justify-content:center; }
    .thanks-actions .btn{ border-radius:14px; font-weight:800; padding:12px 18px; min-width:160px; }
    .btn-thanks-primary{
      background:linear-gradient(135deg,var(--brand),var(--brand2)); border:none; color:#fff;
    }
    .btn-thanks-primary:hover{ color:#fff; filter:brightness(1.05); }
  </style>
</head>
<body>
  <main class="thanks-wrap">
    <div class="thanks-card">
      <?php if (is_file(__DIR__ . '/' . $logo360)): ?>
        <img class="thanks-logo" src="<?= e360($logo360) ?>" alt="360 Estadísticas">
      <?php endif; ?>
      <div class="thanks-icon" aria-hidden="true"><i class="fas fa-check"></i></div>
      <h1>¡Gracias por participar!</h1>
      <p>
        <?php if ($graciasOk): ?>
          Tu respuesta fue registrada correctamente. Agradecemos tu tiempo y tu contribución.
        <?php else: ?>
          Gracias por tu interés. Puedes volver al inicio para ver formularios disponibles.
        <?php endif; ?>
      </p>
      <div class="thanks-actions">
        <a class="btn btn-thanks-primary" href="index.php">Volver al inicio</a>
      </div>
    </div>
  </main>
</body>
</html>
