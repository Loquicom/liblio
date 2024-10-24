<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.home.title') ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/home.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', 'redirect=/') ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <div id="home-content" class="center">
        <h1><?= lang('App.home.welcome') ?> <span class="app-name"><?= setting('App.siteName') ?></span></h1>
        <p><?= lang('App.home.message') ?></p>
        <div class="btn-zone grid">
            <div></div>
            <div><a href="<?= url_to('books') ?>" class="outline" role="button" data-tilt data-tilt-reverse="true"><span class="iconify" data-icon="mdi-book-open-variant"></span> <?= lang('App.common.books') ?></a></div>
            <div><a href="<?= url_to('members/auth') ?>" class="outline" role="button" data-tilt data-tilt-reverse="true"><span class="iconify" data-icon="mdi-history"></span> <?= lang('App.common.history') ?></a></div>
            <div></div>
        </div>
    </div>
<?= $this->endSection() ?>