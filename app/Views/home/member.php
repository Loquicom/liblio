<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.members.detail', [$member['id']]) ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => lang('App.manage.members.detail', [$member['id']]), 'redirect' => '/', 'return' => 'members/auth']) ?>
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
                        <?= lang('App.manage.members.email') ?>
                        <input type="text" value="<?= $member['email'] ?>" disabled>
                    </label>
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
                                    <td><?= $borrow['isbn'] ?></td>
                                    <td><?= $borrow['title'] ?></td>
                                    <td><?= $borrow['author'] ?></td>
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
                                <td><?= $borrow['isbn'] ?></td>
                                <td><?= $borrow['title'] ?></td>
                                <td><?= $borrow['author'] ?></td>
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