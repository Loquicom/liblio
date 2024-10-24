<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.common.login') ?> <?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/login.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <div id="login">
        <h2><a href="<?= url_to('/') ?>"><?= setting('App.siteName') ?></a></h2>
        <article>
            <header><a href="<?= url_to('/') ?>"><span class="iconify" data-icon="mdi-arrow-left"></span></a><?= lang('App.common.login') ?></header>

            <!-- Alert -->
            <?php if (session('message') !== null) : ?>
                <?= view_cell('AlertCell::success', ['message' => session('message')]) ?>
            <?php endif ?>
            <?php if (session('error') !== null) : ?>
                <?= view_cell('AlertCell::error', ['message' => session('error')]) ?>
            <?php elseif (session('errors') !== null) : ?>
                <?php if (is_array(session('errors'))) : ?>
                    <?php foreach (session('errors') as $error) : ?>
                        <?= view_cell('AlertCell::error', ['message' => $error]) ?>
                    <?php endforeach ?>
                <?php else : ?>
                    <?= view_cell('AlertCell::error', ['message' => session('errors')]) ?>
                <?php endif ?>
            <?php endif ?>

            <form action="<?= url_to('members/auth') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Login -->
                <label>
                    <span class="iconify" data-icon="mdi-card-account-details-outline"></span> <?= lang('App.manage.borrow.memberId') ?>
                    <input type="text" name="id" inputmode="id" autocomplete="id" placeholder="<?= lang('App.manage.borrow.memberId') ?>" value="<?= old('id') ?>" required>
                </label>
                <!-- Password -->
                <label>
                    <span class="iconify" data-icon="mdi-email"></span> <?= lang('Auth.email') ?>
                    <input type="email" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
                </label>

                <br/>
                <button type="submit"><?= lang('App.common.login') ?></button>
            </form>

        </article>
    </div>
<?= $this->endSection() ?>