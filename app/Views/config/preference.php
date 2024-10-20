<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.config.preference.title') ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/preference.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => 'App.config.preference.title', 'redirect' => auth()->loggedIn() ? 'manage' : '/', 'return' => auth()->loggedIn() ? 'config' : '/']) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <!-- Color picker -->
    <section>
        <h2><?= lang('App.config.preference.color') ?></h2>
        <figure id="color-picked" class="color-grid">
            <div data-color="red">&xdtri;</div>
            <div data-color="pink">&xdtri;</div>
            <div data-color="fuchsia">&xdtri;</div>
            <div data-color="purple">&xdtri;</div>
            <div data-color="violet">&xdtri;</div>
            <div data-color="indigo">&xdtri;</div>
            <div data-color="blue">&xdtri;</div>
            <div data-color="azure">&xdtri;</div>
            <div data-color="cyan">&xdtri;</div>
            <div data-color="jade">&xdtri;</div>
            <div data-color="green">&xdtri;</div>
            <div data-color="lime">&xdtri;</div>
            <div data-color="yellow">&xdtri;</div>
            <div data-color="amber">&xdtri;</div>
            <div data-color="pumpkin">&xdtri;</div>
            <div data-color="orange">&xdtri;</div>
            <div data-color="sand">&xdtri;</div>
            <div data-color="zinc">&xdtri;</div>
        </figure>
        <figure id="color-picker" class="color-grid">
            <button data-color="red" aria-label="Activate red theme"></button>
            <button data-color="pink" aria-label="Activate pink theme"></button>
            <button data-color="fuchsia" aria-label="Activate fuchsia theme"></button>
            <button data-color="purple" aria-label="Activate purple theme"></button>
            <button data-color="violet" aria-label="Activate violet theme"></button>
            <button data-color="indigo" aria-label="Activate indigo theme"></button>
            <button data-color="blue" aria-label="Activate blue theme"></button>
            <button data-color="azure" aria-label="Activate azure theme"></button>
            <button data-color="cyan" aria-label="Activate cyan theme"></button>
            <button data-color="jade" aria-label="Activate jade theme"></button>
            <button data-color="green" aria-label="Activate green theme"></button>
            <button data-color="lime" aria-label="Activate lime theme"></button>
            <button data-color="yellow" aria-label="Activate yellow theme"></button>
            <button data-color="amber" aria-label="Activate amber theme"></button>
            <button data-color="pumpkin" aria-label="Activate pumpkin theme"></button>
            <button data-color="orange" aria-label="Activate orange theme"></button>
            <button data-color="sand" aria-label="Activate sand theme"></button>
            <button data-color="zinc" aria-label="Activate zinc theme"></button>
        </figure>
        <article>
            <header><h3 id="current-color"></h3></header>
            <div id="demo-title" class="article-title"><?= lang('App.config.preference.demoColor') ?></div>
            <form>
                <fieldset class="grid">
                    <label>
                        <input type="text" placeholder="<?= lang('App.config.preference.field') ?>" />
                    </label>
                    <label id="demo-checkbox">
                        <input type="checkbox" name="<?= lang('App.config.preference.checkbox') ?>" checked />
                        <?= lang('App.config.preference.checkbox') ?>
                    </label>
                    <input type="submit" value="<?= lang('App.config.preference.button') ?>" onclick="event.preventDefault()" />
                </fieldset>
                <fieldset>
                    <label>
                        <input name="terms" type="checkbox" role="switch" checked />
                        <?= lang('App.config.preference.switch') ?>
                    </label>
                </fieldset>
            </form>
        </article>
    </section>
    <hr/>

    <!-- Animation -->
    <section>
        <h2><?= lang('App.config.preference.animation') ?></h2>
        <article>
            <header>
                <h3>
                    <label>
                        <input id="anim-switch" name="animation" type="checkbox" role="switch" onchange="manageAnimation()" />
                        <?= lang('App.config.preference.animation') ?> :
                        <span id="anim-enabled" class="none">
                            <?= lang('App.common.enabled') ?>
                        </span>
                        <span id="anim-disabled" class="none">
                            <?= lang('App.common.disabled') ?>
                        </span>
                    </label>
                </h3>
            </header>
            <div class="article-title"><?= lang('App.config.preference.demoAnimation') ?></div>
            <article class="card center" data-tilt data-tilt-glare data-tilt-max-glare="0.2" data-tilt-reverse="true">
                <header>
                    <span class="iconify" data-icon="mdi-robot-excited"></span>
                </header>
                <?= lang('App.common.example') ?>
            </article>
        </article>
    </section>
    <hr/>

    <!-- Clear data -->
    <section>
        <h2><?= lang('App.config.preference.data') ?></h2>
        <article>
            <header><h3><?= lang('App.config.preference.clear') ?></h3></header>
            <p class="article-title"><?= lang('App.config.preference.dataExplanation') ?></p>
            <div class="center">
                <button class="dialog-open" data-dialog="clear-data"><?= lang('App.config.preference.clear') ?></button>
            </div>
        </article>
    </section>
    <dialog id="clear-data">
        <article>
            <header>
                <button class="dialog-close" aria-label="Close" rel="prev"></button>
                <h2><?= lang('App.config.preference.confirmClear') ?></h2>
            </header>
            <p><?= lang('App.config.preference.messageClear') ?></p>
            <footer>
                <button class="secondary dialog-close"><?= lang('App.common.cancel') ?></button>
                <button class="dialog-close" onclick="localStorage.clear();location.reload()"><?= lang('App.common.confirm') ?></button>
            </footer>
        </article>
    </dialog>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script src="/js/preference.js"></script>
<?= $this->endSection() ?>
