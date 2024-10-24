<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.books.detail', [$book['title']]) ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => lang('App.manage.books.detail', [$book['title']]), 'redirect' => 'manage', 'return' => $return]) ?>
<?= $this->endSection() ?>

<?= $this->section('style') ?>
    <style>
        #add-author {
            font-size: 1.5em;
            padding: .2em .4em;
        }

        .main-details {
            margin-top: 3em;
        }
    </style>
<?= $this->endSection() ?>


<?= $this->section('main') ?>
    <!-- Book infos -->
    <details open>
        <summary><h2><?= lang('App.manage.books.infos') ?></h2></summary>
        <div class="main-details">
            <div class="grid">
                <div>
                    <label>
                        <?= lang('App.manage.books.isbn') ?>
                        <input type="text" value="<?= $book['isbn'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.common.title') ?>
                        <input type="text" value="<?= $book['title'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.books.publisher') ?>
                        <input type="text" value="<?= $book['publisher'] ?>" disabled>
                    </label>
                </div>
            </div>
            <div class="grid">
                <div>
                    <label>
                        <?= lang('App.manage.books.theme') ?>
                        <input type="text" value="<?= $book['theme'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.books.year') ?>
                        <input type="text" value="<?= $book['year'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.books.copy') ?>
                        <input type="text" value="<?= $book['copy'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.books.reference') ?>
                        <input type="text" value="<?= $book['reference'] ?>" disabled>
                    </label>
                </div>
            </div>
            <div>
                <label>
                    <?= lang('App.manage.books.description') ?>
                    <textarea id="description"><?= $book['description'] ?></textarea>
                </label>
            </div>
            <div>
                <label>
                    <?= lang('App.common.comment') ?>
                    <textarea id="comment"><?= $book['comment'] ?></textarea>
                </label>
            </div>
            <div class="grid">
                <div>
                    <ins id="success-text" class="none"><?= lang('App.common.updateSuccess') ?></ins>
                    <mark id="error-text" class="none"></mark>
                </div>
                <div class="right">
                    <button id="save-btn" onclick="save()"><span class="iconify" data-icon="mdi-content-save"></span> <?= lang('App.common.save') ?></button>
                </div>
            </div>
        </div>
    </details>
    <hr/>
    <!-- Book author -->
    <details open>
        <summary><h2><?= lang('App.manage.books.authors') ?></h2></summary>
        <div class="main-details">
            <div class="grid">
                <div>
                    <select id="author">
                        <option value="" selected disabled><?= lang('App.manage.books.selectAuthor') ?></option>
                        <?php foreach($authors as $author): ?>
                            <option id="author-<?= $author['id'] ?>" value="<?= $author['id'] ?>"><?= $author['username'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div>
                    <input id="author-role" type="text" placeholder="<?= lang('App.manage.authors.role') ?>">
                    <small><?= lang('App.common.optional') ?></small>
                </div>
                <div class="none-on-small"></div>
                <div class="right">
                    <button id="add-author" onclick="addAuthor()" data-tooltip="<?= lang('App.common.add') ?>" data-placement="top"><span class="iconify" data-icon="mdi-plus-circle-outline"></span></button>
                </div>
            </div>
            <div class="overflow-auto">
                <table class="striped">
                    <thead>
                        <tr>
                            <td><?= lang('App.manage.authors.username') ?></td>
                            <td><?= lang('App.manage.authors.role') ?></td>
                            <td class="center"><?= lang('App.manage.authors.main') ?></td>
                            <td class="center"><?= lang('App.common.action') ?></td>
                        </tr>
                    </thead>
                    <tbody id="table-author-content">
                        <?php if (count($bookAuthors) > 0): ?>
                            <?php foreach($bookAuthors as $author): ?>
                                <tr data-author="<?= $author['id'] ?>">
                                    <td><?= $author['username'] ?></td>
                                    <td><?= $author['role'] ?></td>
                                    <td class="center"><input type="checkbox" <?= $author['main'] == '1' ? 'checked' : '' ?> disabled></td>
                                    <td class="center">
                                        <a class="secondary" href="<?= url_to('manage/authors' ) . '/' . $author['id']  ?>?return=<?= url_to('manage/books') . '/' . $id ?>" data-tooltip="<?= lang('App.common.detail') ?>" data-placement="top"><span class="iconify" data-icon="mdi-eye"></span></a>
                                        <?php if ($author['main'] == '0'): ?>
                                            <a class="secondary" href="#" data-tooltip="<?= lang('App.common.delete') ?>" data-placement="top" onclick="deleteAuthor(<?= $author['id'] ?>, '<?= $author['username'] ?>')"><span class="iconify" data-icon="mdi-delete"></span></a>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td class="center" colspan="3"><?= lang('App.common.noData') ?></td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </details>
    <hr/>
    <!-- Book borrow -->
    <details open>
        <summary><h2><?= lang('App.manage.books.borrow') ?></h2></summary>
        <div class="main-details">
            <div class="overflow-auto">
                <table class="striped">
                    <thead>
                        <tr>
                            <td><?= lang('App.manage.members.id') ?></td>
                            <td><?= lang('App.manage.members.firstname') ?></td>
                            <td><?= lang('App.manage.members.lastname') ?></td>
                            <td><?= lang('App.manage.members.email') ?></td>
                            <td><?= lang('App.manage.return.outDate') ?></td>
                            <td><?= lang('App.manage.borrow.delay') ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($borrows) > 0): ?>
                            <?php foreach($borrows as $borrow): ?>
                                <tr>
                                    <td><a href="<?= url_to('manage/members') . '/' . $borrow['id'] ?>?return=<?= url_to('manage/books') . '/' . $id ?>"><?= $borrow['id'] ?></a></td>
                                    <td><?= $borrow['firstname'] ?></td>
                                    <td><?= $borrow['lastname'] ?></td>
                                    <td><a href="mailto:<?= $borrow['email'] ?>"><?= $borrow['email'] ?></a></td>
                                    <td><?= $borrow['out_date'] ?></td>
                                    <td><?= $borrow['delay'] ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td class="center" colspan="6"><?= lang('App.common.noData') ?></td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </details>
    <hr/>
    <!-- Book old borrow -->
    <details open>
        <summary><h2><?= lang('App.manage.books.oldBorrow') ?></h2></summary>
        <div class="main-details">
            <div class="overflow-auto">
                <table class="striped">
                    <thead>
                    <tr>
                        <td><?= lang('App.manage.members.id') ?></td>
                        <td><?= lang('App.manage.members.firstname') ?></td>
                        <td><?= lang('App.manage.members.lastname') ?></td>
                        <td><?= lang('App.manage.members.email') ?></td>
                        <td><?= lang('App.manage.return.outDate') ?></td>
                        <td><?= lang('App.manage.return.returnDate') ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($oldBorrows) > 0): ?>
                        <?php foreach($oldBorrows as $borrow): ?>
                            <tr>
                                <td><a href="<?= url_to('manage/members') . '/' . $borrow['id'] ?>?return=<?= url_to('manage/books') . '/' . $id ?>"><?= $borrow['id'] ?></a></td>
                                <td><?= $borrow['firstname'] ?></td>
                                <td><?= $borrow['lastname'] ?></td>
                                <td><a href="mailto:<?= $borrow['email'] ?>"><?= $borrow['email'] ?></a></td>
                                <td><?= $borrow['out_date'] ?></td>
                                <td><?= $borrow['return_date'] ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td class="center" colspan="6"><?= lang('App.common.noData') ?></td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </details>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script>
        const id = '<?= $id ?>';
        const authorReturnURL = '<?= url_to('manage/authors' ) . '/' ?>';
        const lang = {
            detail: '<?= lang('App.common.detail') ?>',
            del: '<?= lang('App.common.delete') ?>',
            errorAjax: '<?= lang('App.common.errorAjax') ?>',
            createSuccess: '<?= lang('App.common.createSuccess') ?>',
            updateSuccess: '<?= lang('App.common.updateSuccess') ?>',
            deleteSuccess: '<?= lang('App.common.deleteSuccess') ?>',
        };
    </script>
    <script src="/js/detail-book.js"></script>
<?= $this->endSection() ?>
