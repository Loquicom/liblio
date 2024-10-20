<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.account.title') ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/account.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', 'redirect=manage, return=manage, title=App.account.title') ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <?php if (session('success') !== null) : ?>
        <section>
            <?= view_cell('AlertCell::success', ['message' => session('success')]) ?>
        </section>
    <?php endif ?>
    <section>
        <h2><?= lang('App.account.informations') ?></h2>
        <article>
            <!-- Alert -->
            <?php if (session('info-warn') !== null) : ?>
                <?= view_cell('AlertCell::warn', ['message' => session('info-warn')]) ?>
            <?php endif ?>
            <?php if (session('info-errors') !== null) : ?>
                <?php if (is_array(session('info-errors'))) : ?>
                    <?php foreach (session('info-errors') as $error) : ?>
                        <?= view_cell('AlertCell::error', ['message' => $error]) ?>
                    <?php endforeach ?>
                <?php else : ?>
                    <?= view_cell('AlertCell::error', ['message' => session('info-errors')]) ?>
                <?php endif ?>
            <?php endif ?>
            <!-- Form -->
            <form id="form-info" action="<?= url_to('account') ?>" method="post">
                <label>
                    <span class="iconify" data-icon="mdi-account"></span> <?= lang('Auth.username') ?>
                    <input type="text" name="username" inputmode="username" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?? $username ?>" required>
                </label>
                <label>
                    <span class="iconify" data-icon="mdi-email"></span> <?= lang('Auth.email') ?>
                    <input type="email" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?? $email ?>" required>
                </label>

                <?= csrf_field() ?>
                <button id="form-info-btn" class="none"></button>
            </form>
            <footer class="right">
                <button onclick="document.getElementById('form-info-btn').click()"><span class="iconify" data-icon="mdi-content-save"></span> <?= lang('App.common.save') ?></button>
            </footer>
        </article>
    </section>
    <hr/>
    <section>
        <h2><?= lang('App.account.password') ?></h2>
        <article>
            <!-- Alert -->
            <?php if (session('pass-errors') !== null) : ?>
                <?php if (is_array(session('pass-errors'))) : ?>
                    <?php foreach (session('pass-errors') as $error) : ?>
                        <?= view_cell('AlertCell::error', ['message' => $error]) ?>
                    <?php endforeach ?>
                <?php else : ?>
                    <?= view_cell('AlertCell::error', ['message' => session('pass-errors')]) ?>
                <?php endif ?>
            <?php endif ?>
            <!-- Form -->
            <form id="form-pass" action="<?= url_to('account') ?>" method="post">
                <label>
                    <span class="iconify" data-icon="mdi-lock-clock"></span> <?= lang('App.account.passwordOld') ?>
                    <input type="password" name="password_old" inputmode="text" autocomplete="password" placeholder="<?= lang('App.account.passwordOld') ?>" required>
                </label>
                <label>
                    <span class="iconify" data-icon="mdi-lock"></span> <?= lang('Auth.password') ?>
                    <input type="password" name="password" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.password') ?>" value="<?= old('password') ?>" required>
                </label>
                <label>
                    <span class="iconify" data-icon="mdi-lock-reset"></span> <?= lang('Auth.passwordConfirm') ?>
                    <input type="password" name="password_confirm" inputmode="text" autocomplete="new-password" placeholder="<?= lang('Auth.passwordConfirm') ?>" value="<?= old('password_confirm') ?>" required>
                </label>

                <?= csrf_field() ?>
                <button id="form-pass-btn" class="none"></button>
            </form>
            <footer class="right">
                <button onclick="document.getElementById('form-pass-btn').click()"><span class="iconify" data-icon="mdi-content-save"></span> <?= lang('App.common.save') ?></button>
            </footer>
        </article>
    </section>
<?= $this->endSection() ?>