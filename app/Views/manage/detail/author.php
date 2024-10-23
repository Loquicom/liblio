<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.authors.detail', [$author]) ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => lang('App.manage.authors.detail', [$author]), 'redirect' => 'manage', 'return' => $return]) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <h2><?= lang('App.manage.authors.books') ?></h2>
    <div class="overflow-auto">
        <table class="striped">
            <thead>
            <tr>
                <td><?= lang('App.manage.books.isbn') ?></td>
                <td><?= lang('App.common.title') ?></td>
                <td><?= lang('App.manage.books.author') ?></td>
                <td><?= lang('App.manage.authors.role') ?></td>
                <td><?= lang('App.manage.books.publisher') ?></td>
                <td><?= lang('App.manage.books.theme') ?></td>
                <td><?= lang('App.manage.books.year') ?></td>
                <td><?= lang('App.manage.books.reference') ?></td>
            </tr>
            </thead>
            <tbody>
            <?php if (count($books) > 0): ?>
                <?php foreach($books as $book): ?>
                    <tr>
                        <td><a href="<?= url_to('manage/books' ) . '/' . $book['isbn']  ?>?return=<?= url_to('manage/authors') . '/' . $id ?>"><?= $book['isbn'] ?></a></td>
                        <td><?= $book['title'] ?></td>
                        <td><?= $book['author'] ?></td>
                        <td><?= $book['role'] ?></td>
                        <td><?= $book['publisher'] ?></td>
                        <td><?= $book['theme'] ?></td>
                        <td><?= $book['year'] ?></td>
                        <td><?= $book['reference'] ?></td>
                    </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr>
                    <td class="center" colspan="7"><?= lang('App.common.noData') ?></td>
                </tr>
            <?php endif ?>
            </tbody>
        </table>
    </div>
<?= $this->endSection() ?>
