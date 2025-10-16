<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>KissAI Front – Portail Applicatif</title>
    <meta name="description" content="Portail des applications internes – Banque Cameroun">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="/assets/css/bulma.min.css">
    <style>
      .hero.is-bank{background:linear-gradient(135deg,#0a2463,#1c4f9c);color:#fff}
    </style>
</head>
<body>
  <section class="hero is-bank">
    <div class="hero-body">
      <p class="title">Portail des Applications</p>
      <p class="subtitle">Banque – Cameroun</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="columns">
        <div class="column is-two-thirds">
          <div class="box">
            <h2 class="title is-5">Rechercher une application</h2>
            <form method="post" action="/apps/search">
              <div class="field has-addons">
                <div class="control is-expanded">
                  <input class="input" type="text" name="q" placeholder="Nom de l’application...">
                </div>
                <div class="control">
                  <button class="button is-link" type="submit">Chercher</button>
                </div>
              </div>
            </form>
          </div>

          <div class="box">
            <h2 class="title is-5">Accès par OTP</h2>
            <form method="post" action="/apps/request-otp">
              <div class="field">
                <label class="label">Email professionnel</label>
                <div class="control">
                  <input class="input" type="email" required name="email" placeholder="prenom.nom@entreprise.cm">
                </div>
              </div>
              <div class="field">
                <label class="label">Identifiant application</label>
                <div class="control">
                  <input class="input" type="text" required name="application_id" placeholder="ID de l’application">
                </div>
              </div>
              <div class="field">
                <button class="button is-primary" type="submit">Recevoir OTP</button>
              </div>
            </form>
          </div>
        </div>
        <div class="column">
          <article class="message is-info">
            <div class="message-header">
              <p>Information</p>
            </div>
            <div class="message-body">
              Entrez votre email pour recevoir un OTP d’accès. L’audit est activé et détaillé.
            </div>
          </article>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="content has-text-centered">
      <p>Environnement: <?= ENVIRONMENT ?> | Rendu en {elapsed_time}s</p>
    </div>
  </footer>

  <script src="/assets/js/app.js"></script>
</body>
</html>
