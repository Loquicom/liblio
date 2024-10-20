<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/login.css" />
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <div id="login">
        <h2><a href="<?= url_to('/') ?>"><?= setting('App.siteName') ?></a></h2>
        <article>
            <header><a href="<?= url_to('/') ?>"><span class="iconify" data-icon="mdi-arrow-left"></span></a><?= lang('Auth.login') ?></header>

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

            <form action="<?= url_to('login') ?>" method="post">
                <?= csrf_field() ?>

                <!-- Login -->
                <label>
                    <span class="iconify" data-icon="mdi-account"></span> <?= lang('Auth.username') ?>
                    <input type="text" name="username" inputmode="username" autocomplete="username" placeholder="<?= lang('Auth.username') ?>" value="<?= old('username') ?>" required>
                </label>
                <!-- Password -->
                <label>
                    <span class="iconify" data-icon="mdi-lock"></span> <?= lang('Auth.password') ?>
                    <input type="password" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>" required>
                </label>

                <!-- Remember me -->
                <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                    <label>
                        <input type="checkbox" name="remember" <?php if (old('remember')): ?> checked<?php endif ?>/>
                        <?= lang('Auth.rememberMe') ?>
                    </label>
                <?php endif; ?>

                <!-- Magic link -->
                <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                    <br/>
                    <p class="text-center"><?= lang('Auth.forgotPassword') ?> <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.useMagicLink') ?></a></p>
                <?php endif ?>

                <!-- Register -->
                <?php if (setting('Auth.allowRegistration')) : ?>
                    <?= (setting('Auth.allowMagicLinkLogins')) ? '' : '<br/>' ?>
                    <p class="text-center"><?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a></p>
                <?php endif ?>

                <br/>
                <button type="submit"><?= lang('Auth.login') ?></button>
            </form>

        </article>
    </div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script>
        localStorage.removeItem('jwt');
    </script>
<?= $this->endSection() ?>