<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.authors.detail', [$author]) ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
<?= view_cell('HeaderCell', ['title' => lang('App.manage.authors.detail', [$author]), 'redirect' => 'manage', 'return' => 'manage/authors']) ?>
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
                    <tr class="cursor-pointer" onclick="location.href = '<?= url_to('manage/books' ) . '/' . $book['isbn']  ?>'">
                        <td class="cursor-pointer"><?= $book['isbn'] ?></td>
                        <td class="cursor-pointer"><?= $book['title'] ?></td>
                        <td class="cursor-pointer"><?= $book['author'] ?></td>
                        <td class="cursor-pointer"><?= $book['role'] ?></td>
                        <td class="cursor-pointer"><?= $book['publisher'] ?></td>
                        <td class="cursor-pointer"><?= $book['theme'] ?></td>
                        <td class="cursor-pointer"><?= $book['year'] ?></td>
                        <td class="cursor-pointer"><?= $book['reference'] ?></td>
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
