<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang('App.manage.return.title') ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/borrow.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => lang('App.manage.return.title'), 'redirect' => 'manage', 'return' => 'manage']) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <!-- Alert -->
    <div id="alert-error" class="none">
        <?= view_cell('AlertCell::error', ['message' => '']) ?>
    </div>
    <div id="alert-success" class="none">
        <?= view_cell('AlertCell::success', ['message' => '']) ?>
    </div>
    <!-- Select member -->
    <section>
        <h2><?= lang('App.manage.borrow.selectMember') ?></h2>
        <div class="grid">
            <div>
                <label for="member"><?= lang('App.manage.borrow.memberId') ?></label>
                <fieldset role="group">
                    <input id="member" name="member" type="text" placeholder="<?= lang('App.manage.borrow.memberId') ?>" autocomplete="none" onchange="getMember()" />
                    <button id="search-member" class="outline dialog-open" data-dialog="dialog-member"><span class="iconify" data-icon="mdi-search"></span></button>
                </fieldset>
            </div>
            <div>
                <label>
                    <?= lang('App.manage.borrow.memberFirstname') ?>
                    <input id="firstname" name="firstname" type="text" placeholder="<?= lang('App.manage.borrow.memberFirstname') ?>" disabled/>
                </label>
            </div>
            <div>
                <label>
                    <?= lang('App.manage.borrow.memberLastname') ?>
                    <input id="lastname" name="lastname" type="text" placeholder="<?= lang('App.manage.borrow.memberLastname') ?>" disabled/>
                </label>
            </div>
        </div>
    </section>
    <hr/>
    <!-- Select books -->
    <section>
        <h2><?= lang('App.manage.borrow.selectBooks') ?></h2>
        <div class="overflow-auto">
            <table class="striped">
                <thead>
                <tr>
                    <td><?= lang('App.manage.borrow.bookISBN') ?></td>
                    <td><?= lang('App.manage.borrow.bookTitle') ?></td>
                    <td><?= lang('App.manage.borrow.bookAuthor') ?></td>
                    <td><?= lang('App.manage.borrow.bookPublisher') ?></td>
                    <td><?= lang('App.manage.return.outDate') ?></td>
                    <td class="center"><?= lang('App.common.select') ?></td>
                </tr>
                </thead>
                <tbody id="books-data">
                <tr id="no-data-row" class="tr-value">
                    <td class="center" colspan="6"><?= lang('App.common.noData') ?></td>
                </tr>
                <tr id="loading-row" class="none">
                    <td class="center" colspan="6" aria-busy="true"><?= lang('App.common.loading') ?></td>
                </tr>
                <tr id="error-row" class="none">
                    <td class="center" colspan="6"><?= lang('App.manage.return.loadError') ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="grid">
            <div class="none-on-small"></div>
            <div class="none-on-small"></div>
            <div class="none-on-small"></div>
            <div id="btn-valid-container">
                <button id="btn-valid" onclick="sendData()" disabled><span class="iconify" data-icon="mdi-check-bold"></span><?= lang('App.common.valid') ?></button>
            </div>
        </div>
    </section>

    <!-- Modal search member -->
    <dialog id="dialog-member">
        <article>
            <header>
                <button class="dialog-close" aria-label="Close" rel="prev"></button>
                <h2><?= lang('App.manage.borrow.memberFind') ?></h2>
            </header>
            <div id="dialog-member-search">
                <div class="grid">
                    <label>
                        <?= lang('App.manage.members.firstname') ?>
                        <input id="dialog-member-firstname" name="dialog-member-firstname" type="text" placeholder="<?= lang('App.manage.members.firstname') ?>"/>
                    </label>
                    <label>
                        <?= lang('App.manage.members.lastname') ?>
                        <input id="dialog-member-lastname" name="dialog-member-lastname" type="text" placeholder="<?= lang('App.manage.members.lastname') ?>"/>
                    </label>
                </div>
                <button id="dialog-member-btn-search" onclick="loadMembers()"><span class="iconify" data-icon="mdi-account-search"></span><?= lang('App.common.search') ?></button>
            </div>
            <div class="overflow-auto">
                <table class="striped">
                    <thead>
                    <tr>
                        <th><?= lang('App.manage.members.id') ?></th>
                        <th><?= lang('App.manage.members.firstname') ?></th>
                        <th><?= lang('App.manage.members.lastname') ?></th>
                    </tr>
                    </thead>
                    <tbody id="dialog-member-data">
                    <tr id="dialog-member-loading-row" class="none">
                        <td class="center" colspan="3" aria-busy="true"><?= lang('App.common.loading') ?></td>
                    </tr>
                    <tr id="dialog-member-no-data-row">
                        <td class="center" colspan="3"><?= lang('App.common.noData') ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <footer>
                <button class="secondary dialog-close"><?= lang('App.common.cancel') ?></button>
                <button id="dialog-member-btn-select" class="dialog-close" onclick="selectMemberFromSearch()"><?= lang('App.common.select') ?></button>
            </footer>
        </article>
    </dialog>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script>
        const lang = {
            errorAjax: '<?= lang('App.common.errorAjax') ?>',
            saveSuccess: '<?= lang('App.common.saveSuccess') ?>',
        }
    </script>
    <script src="/js/return.js"></script>
<?= $this->endSection() ?>
