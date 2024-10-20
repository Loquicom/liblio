<nav>
    <ul>
        <li class="none-on-small">
            <span class="nav-title">
                <?php if ($return != null): ?>
                    <a class="back" href="<?= url_to($return) ?>" data-tooltip="<?= lang('App.return.' . $return) ?>" data-placement="right"><span class="iconify" data-icon="mdi-arrow-left"></a>
                <?php endif ?>
                <a href="<?= url_to($redirect) ?>"><?= setting('App.siteName') . (($title != null) ? '&nbsp;-&nbsp;' . lang($title) : '') ?></a>
            </span>
        </li>
        <li class="none-on-large">
            <span class="nav-title">
                <a href="<?= url_to($redirect) ?>"><?= ($title != null) ? lang($title) : '' ?></a>
            </span>
        </li>
    </ul>
    <ul>
        <?php if (auth()->loggedIn()): ?>
            <li><a href="<?= url_to('config') ?>" class="contrast" data-tooltip="<?= lang('App.header.config') ?>" data-placement="bottom"><span class="iconify" data-icon="mdi-cog"></span></a></li>
            <li><a href="<?= url_to('account') ?>" class="contrast" data-tooltip="<?= lang('App.header.account') ?>" data-placement="bottom"><span class="iconify" data-icon="mdi-account"></span></a></li>
            <li><a href="<?= url_to('logout') ?>" class="contrast" data-tooltip="<?= lang('App.header.logout') ?>" data-placement="left"><span class="iconify" data-icon="mdi-logout"></span></a></li>
        <?php else: ?>
            <li><a href="<?= url_to('preference') ?>" class="contrast" data-tooltip="<?= lang('App.header.preference') ?>" data-placement="bottom"><span class="iconify" data-icon="mdi-cog"></span></a></li>
            <li><a href="<?= url_to('login') ?>" class="contrast" data-tooltip="<?= lang('App.header.login') ?>" data-placement="left"><span class="iconify" data-icon="mdi-account-lock"></span></a></li>
        <?php endif ?>
    </ul>
</nav>