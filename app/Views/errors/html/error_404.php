<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>
    <link rel="stylesheet" href="/assets/css/bulma.min.css">
</head>
<body>
  <section class="section">
    <div class="container">
      <div class="notification is-danger is-light">
        <h1 class="title is-4">404</h1>
        <p>
          <?php if (ENVIRONMENT !== 'production') : ?>
              <?= nl2br(esc($message)) ?>
          <?php else : ?>
              <?= lang('Errors.sorryCannotFind') ?>
          <?php endif; ?>
        </p>
      </div>
    </div>
  </section>
</body>
</html>
