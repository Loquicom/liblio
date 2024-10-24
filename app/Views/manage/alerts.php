<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.alerts.title') ?><?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => lang('App.manage.alerts.title'), 'redirect' => 'manage', 'return' => 'manage']) ?>
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
    .main-details {
        margin-top: 3em;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <details open>
        <summary><h2><?= lang('App.manage.alerts.overdue') ?></h2></summary>
        <div class="main-details overflow-auto">
            <table class="striped">
                <thead>
                <tr>
                    <td><?= lang('App.manage.borrow.memberId') ?></td>
                    <td><?= lang('App.manage.members.firstname') ?></td>
                    <td><?= lang('App.manage.members.lastname') ?></td>
                    <td><?= lang('App.manage.books.isbn') ?></td>
                    <td><?= lang('App.common.title') ?></td>
                    <td><?= lang('App.manage.members.email') ?></td>
                    <td><?= lang('App.manage.return.outDate') ?></td>
                    <td><?= lang('App.manage.return.returnDateMax') ?></td>
                </tr>
                </thead>
                <tbody>
                <?php if (count($alerts['overdue']) > 0): ?>
                    <?php foreach($alerts['overdue'] as $alert): ?>
                        <tr>
                            <td><a href="<?= url_to('manage/members' ) . '/' . $alert['member']  ?>?return=manage/alerts"><?= $alert['member'] ?></a></td>
                            <td><?= $alert['firstname'] ?></td>
                            <td><?= $alert['lastname'] ?></td>
                            <td><a href="<?= url_to('manage/books' ) . '/' . $alert['isbn']  ?>?return=manage/alerts"><?= $alert['isbn'] ?></a></td>
                            <td><?= $alert['title'] ?></td>
                            <td><a href="mailto:<?= $alert['email'] ?>"><?= $alert['email'] ?></a></td>
                            <td><?= $alert['out_date'] ?></td>
                            <td><?= date('Y-m-d', strtotime($alert['out_date']) + ($alert['delay'] * 24 * 3600)) ?></td>
                        </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr>
                        <td class="center" colspan="8"><?= lang('App.common.noData') ?></td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </div>
    </details>
<?= $this->endSection() ?>
