<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang($title) ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/menu.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => $title, 'redirect' => auth()->loggedIn() ? 'manage' : '/', 'return' => $return ?? null]) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <?php for($i = 0; $i < count($menus); $i++): ?>
        <?php if ($i % 5 == 0): ?>
            <div class="grid">
        <?php endif ?>
        <div>
            <article class="card center" data-tilt data-tilt-glare data-tilt-max-glare="0.2" data-tilt-reverse="true" data-link="<?= $menus[$i]['link'] ?? '#' ?>">
                <header>
                    <span class="iconify" data-icon="mdi-<?= $menus[$i]['icon'] ?? 'help' ?>"></span>
                </header>
                    <?= isset($menus[$i]['name']) ? lang($menus[$i]['name']) : '?' ?>
            </article>
        </div>
        <?php if ($i % 5 == 4): ?>
            </div>
        <?php endif ?>
    <?php endfor ?>
    <?php for(; $i % 5 != 0; $i++): ?>
        <div></div>
        <?php if ($i % 5 == 4): ?>
            </div>
        <?php endif ?>
    <?php endfor ?>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script>
        const cards = document.getElementsByClassName("card");
        for (const card of cards) {
            card.addEventListener('click', () => {
                window.location.href = card.getAttribute('data-link');
            })
        }
    </script>
<?= $this->endSection() ?>
