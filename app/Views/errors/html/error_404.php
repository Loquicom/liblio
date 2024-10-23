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
    <style>
        #content {
            margin-top: 30vh;
            transform: translateY(-50%);
        }

        #konami-helper {
            opacity: 0.4;
            font-size: 0.5em;
        }
    </style>
    <script>
        let color = localStorage.getItem('color');
        if (color) {
            document.getElementById('picocss').setAttribute('href', `/css/picocss/pico.${color}.css`);
        }
    </script>

    <title><?= setting('App.siteName') ?> - 404</title>
</head>
<body>
<?= view_cell('HeaderCell', 'title=404, redirect=/, return=/') ?>

<main class="container">
    <div id="content" class="center">
        <h1>404</h1>
        <p><?= lang('App.404.text') ?></p>
        <span id="konami-helper" data-tooltip="<?= lang('App.404.hint') ?>" data-placement="bottom">&uarr;&uarr;&darr;&darr;&larr;&rarr;&larr;&rarr;BA</span>
    </div>
    <div id="game" class="center none">
        <iframe src="https://www.lexaloffle.com/bbs/widget.php?pid=sp8ce_em_up" allowfullscreen width="621" height="513" style="border:none; overflow:hidden"></iframe>
    </div>
</main>

<footer id="main-footer">
    <?= setting('App.siteName') ?>, <?= lang('App.footer.designed') ?> Loquicom, <?= lang('App.footer.with') ?> <a href="https://codeigniter.com" target="_blank">CodeIgniter 4</a>, <a href="https://picocss.com" target="_blank">Pico CSS</a> <?= lang('App.footer.and') ?> <a href="https://pictogrammers.com/library/mdi/" target="_blank">Pictogramers MDI</a>. <?= lang('App.footer.available', ['https://github.com/Loquicom/liblio']) ?>. <?= lang('App.footer.legal', [url_to('legal')]) ?>.
</footer>

<script src="//code.iconify.design/1/1.0.6/iconify.min.js"></script>
<script src="/js/theme-switcher.min.js"></script>
<script src="/js/konami.js"></script>
<script>
    themeSwitcher('<?= lang('App.lightToDark') ?>', '<?= lang('App.darkToLight') ?>');
    new Konami(() => {
        document.getElementById('content').classList.add('none');
        document.getElementById('game').classList.remove('none');
    });
</script>
</body>
</html>