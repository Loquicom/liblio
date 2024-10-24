<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.members.detail', [$member['firstname'] . ' ' . $member['lastname']]) ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => lang('App.manage.members.detail', [$member['firstname'] . ' ' . $member['lastname']]), 'redirect' => 'manage', 'return' => $return]) ?>
<?= $this->endSection() ?>

<?= $this->section('style') ?>
    <style>
        .main-details {
            margin-top: 3em;
        }
    </style>
<?= $this->endSection() ?>


<?= $this->section('main') ?>
    <!-- Member infos -->
    <details open>
        <summary><h2><?= lang('App.manage.members.infos') ?></h2></summary>
        <div class="main-details">
            <div class="grid">
                <div>
                    <label>
                        <?= lang('App.manage.members.id') ?>
                        <input type="text" value="<?= $member['id'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.members.firstname') ?>
                        <input type="text" value="<?= $member['firstname'] ?>" disabled>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.members.lastname') ?>
                        <input type="text" value="<?= $member['lastname'] ?>" disabled>
                    </label>
                </div>
            </div>
            <div class="grid">
                <div>
                    <label>
                        <?= lang('App.manage.members.email') ?>
                        <span role="group">
                            <input type="text" value="<?= $member['email'] ?>" disabled>
                            <a role="button" class="outline" href="mailto:<?= $member['email'] ?>" data-tooltip="<?= lang('App.common.sendEmail') ?>"><span class="iconify" data-icon="mdi-email"></span></a>
                        </span>
                    </label>
                </div>
                <div>
                    <label>
                        <?= lang('App.manage.members.createdAt') ?>
                        <input type="text" value="<?= $member['created_at'] ?>" disabled>
                    </label>
                </div>
            </div>
            <div>
                <label>
                    <?= lang('App.common.comment') ?>
                    <textarea id="comment"><?= $member['comment'] ?></textarea>
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
    <!-- Book borrow -->
    <details open>
        <summary><h2><?= lang('App.manage.books.borrow') ?></h2></summary>
        <div class="main-details">
            <div class="overflow-auto">
                <table class="striped">
                    <thead>
                        <tr>
                            <td><?= lang('App.manage.books.isbn') ?></td>
                            <td><?= lang('App.common.title') ?></td>
                            <td><?= lang('App.manage.books.author') ?></td>
                            <td><?= lang('App.manage.books.publisher') ?></td>
                            <td><?= lang('App.manage.return.outDate') ?></td>
                            <td><?= lang('App.manage.return.returnDateMax') ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($borrows) > 0): ?>
                            <?php foreach($borrows as $borrow): ?>
                                <tr>
                                    <td><a href="<?= url_to('manage/books') . '/' . $borrow['isbn'] ?>?return=<?= url_to('manage/members') . '/' . $id ?>"><?= $borrow['isbn'] ?></a></td>
                                    <td><?= $borrow['title'] ?></td>
                                    <td><a href="<?= url_to('manage/authors') . '/' . $borrow['author_id'] ?>?return=<?= url_to('manage/members') . '/' . $id ?>"><?= $borrow['author'] ?></a></td>
                                    <td><?= $borrow['publisher'] ?></td>
                                    <td><?= $borrow['out_date'] ?></td>
                                    <td><?= date('Y-m-d', strtotime($borrow['out_date']) + ($borrow['delay'] * 24 * 3600)) ?></td>
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
                        <td><?= lang('App.manage.books.isbn') ?></td>
                        <td><?= lang('App.common.title') ?></td>
                        <td><?= lang('App.manage.books.author') ?></td>
                        <td><?= lang('App.manage.books.publisher') ?></td>
                        <td><?= lang('App.manage.return.outDate') ?></td>
                        <td><?= lang('App.manage.return.returnDate') ?></td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($oldBorrows) > 0): ?>
                        <?php foreach($oldBorrows as $borrow): ?>
                            <tr>
                                <td><a href="<?= url_to('manage/books') . '/' . $borrow['isbn'] ?>?return=<?= url_to('manage/members') . '/' . $id ?>"><?= $borrow['isbn'] ?></a></td>
                                <td><?= $borrow['title'] ?></td>
                                <td><a href="<?= url_to('manage/authors') . '/' . $borrow['author_id'] ?>?return=<?= url_to('manage/members') . '/' . $id ?>"><?= $borrow['author'] ?></a></td>
                                <td><?= $borrow['publisher'] ?></td>
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
        const lang = {
            detail: '<?= lang('App.common.detail') ?>',
            del: '<?= lang('App.common.delete') ?>',
            errorAjax: '<?= lang('App.common.errorAjax') ?>',
            createSuccess: '<?= lang('App.common.createSuccess') ?>',
            updateSuccess: '<?= lang('App.common.updateSuccess') ?>',
            deleteSuccess: '<?= lang('App.common.deleteSuccess') ?>',
        };
    </script>
    <script src="/js/detail-member.js"></script>
<?= $this->endSection() ?>
