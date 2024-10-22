<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.legal.title') ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', 'redirect=/, return=/, title='.lang('App.legal.title')) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <p><?= lang('App.legal.text') ?></p>
<?= $this->endSection() ?>