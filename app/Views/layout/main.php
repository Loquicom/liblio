<!doctype html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light dark" />

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link id="picocss" rel="stylesheet" href="/css/picocss/pico.amber.css" />
    <link rel="stylesheet" href="/css/style.css" />
    <link rel="stylesheet" href="/css/theme-switcher.css" />
    <?= $this->renderSection('style') ?>
    <script>
        let color = localStorage.getItem('color');
        if (color) {
            document.getElementById('picocss').setAttribute('href', `/css/picocss/pico.${color}.css`);
        }
    </script>

    <title><?= setting('App.siteName') ?> - <?= $this->renderSection('title') ?></title>
</head>
<body>
    <?= $this->renderSection('header') ?>

    <main class="container">
        <?= $this->renderSection('main') ?>
    </main>

    <footer id="main-footer">
        <?= setting('App.siteName') ?>, <?= lang('App.footer.designed') ?> Loquicom, <?= lang('App.footer.with') ?> <a href="https://codeigniter.com" target="_blank">CodeIgniter 4</a>, <a href="https://picocss.com" target="_blank">Pico CSS</a> <?= lang('App.footer.and') ?> <a href="https://pictogrammers.com/library/mdi/" target="_blank">Pictogramers MDI</a>. <?= lang('App.footer.available', ['https://github.com/Loquicom/liblio']) ?>. <?= lang('App.footer.legal', [url_to('legal')]) ?>.
    </footer>

    <script src="//code.iconify.design/1/1.0.6/iconify.min.js"></script>
    <script src="/js/theme-switcher.min.js"></script>
    <script src="/js/vanilla-tilt.min.js"></script>
    <script src="/js/script.js"></script>
    <?= $this->renderSection('script') ?>
    <script>
        themeSwitcher('<?= lang('App.lightToDark') ?>', '<?= lang('App.darkToLight') ?>');
    </script>
</body>
</html>