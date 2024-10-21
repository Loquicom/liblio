<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>WIP<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', 'redirect=/, title=WIP') ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <h1>WIP</h1>
<?= $this->endSection() ?>