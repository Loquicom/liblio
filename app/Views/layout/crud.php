<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang($title) ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/crud.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => $title, 'redirect' => auth()->loggedIn() ? 'manage' : '/', 'return' => $return ?? null]) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <!-- Search -->
    <section id="search">
        <div class="center">
            <div id="simple-search" role="search">
                <input id="simple-search-input" name="search" type="search" placeholder="<?= lang('App.common.search') ?>" />
                <button id="search-btn" onclick="loadData()"><span class="iconify" data-icon="mdi-send"></span></button>
            </div>
        </div>
        <details id="advanced-search">
            <summary onclick="setSearchMode()"><?= lang('App.common.advancedSearch') ?></summary>
            <form id="advanced-search-form">
                <?php $i = 0 ?>
                <?php foreach ($fields as $key => $field): ?>
                    <?php if (!$field['search']) continue ?>
                    <?php if ($i % 3 == 0): ?>
                        <fieldset class="grid">
                    <?php endif ?>
                    <?php if (is_array($field['type'])): ?>
                        <label>
                            <?= lang($field['lib']) ?>
                            <select id="advanced-search-<?= $key ?>" name="<?= $key ?>">
                                <option selected value=""></option>
                                <?php foreach ($field['type'] as $k => $val): ?>
                                    <?php if ($field['col']): ?>
                                        <option value="<?= $k ?>"><?= lang($val) ?></option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </label>
                    <?php else: ?>
                        <label>
                            <?= lang($field['lib']) ?>
                            <input id="advanced-search-<?= $key ?>" type="<?= $field['type'] ?>" name="<?= $key ?>">
                        </label>
                    <?php endif ?>
                    <?php if ($i++ % 3 == 2): ?>
                        </fieldset>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if (($i-1) % 3 != 2): ?>
                    </fieldset>
                <?php endif ?>
            </form>
        </details>
    </section>
    <!-- Sort & Add -->
    <section>
        <div class="grid">
            <div>
                <select id="sort-select" name="sort" aria-label="Sort columns">
                    <option value="" selected><?= lang('App.common.noSort') ?></option>
                    <?php foreach ($fields as $key => $field): ?>
                        <?php if ($field['col']): ?>
                            <option value="<?= $key ?>"><?= lang($field['lib']) ?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="none-on-small"></div>
            <div class="none-on-small"></div>
            <div class="right">
                <?php if ($mode === 'edit'): ?>
                    <button class="dialog-open" id="add" data-tooltip="<?= lang('App.common.add') ?>" data-placement="top" data-dialog="dialog-edit" onclick="selectedId=null;clearDialog()"><span class="iconify" data-icon="mdi-plus-circle-outline"></span></button>
                <?php endif ?>
            </div>
        </div>
    </section>
    <!-- Alert -->
    <div id="alert-error" class="none">
        <?= view_cell('AlertCell::error', ['message' => '']) ?>
    </div>
    <div id="alert-success" class="none">
        <?= view_cell('AlertCell::success', ['message' => '']) ?>
    </div>
    <!-- Result -->
    <section>
        <table class="striped">
            <thead>
            <tr>
                <?php foreach ($fields as $key => $field): ?>
                    <?php if ($field['col']): ?>
                        <th scope="col"><?= lang($field['lib']) ?></th>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if ($mode === 'edit'): ?>
                    <th class="center" scope="col"><?= lang('App.common.action') ?></th>
                <?php endif ?>
            </tr>
            </thead>
            <tbody id="table-content">
                <tr id="loading-row">
                    <td class="center" colspan="<?= $mode === 'edit' ? count($fields) + 1 : count($fields) ?>" aria-busy="true"><?= lang('App.common.loading') ?></td>
                </tr>
                <tr id="no-data-row" class="none">
                    <td class="center" colspan="<?= $mode === 'edit' ? count($fields) + 1 : count($fields) ?>"><?= lang('App.common.noData') ?></td>
                </tr>
            </tbody>
        </table>
    </section>
    <!-- Pagination -->
    <section>
        <div class="grid">
            <div id="page-size">
                <select id="page-size-select" name="size" aria-label="Page size" onchange="changePageSize()">
                    <option selected>10</option>
                    <option>20</option>
                    <option>50</option>
                    <option>100</option>
                    <option>200</option>
                </select>
            </div>
            <div class="none-on-small"></div>
            <div class="none-on-small"></div>
            <div id="page-select" role="group">
                <button id="prev-page" class="secondary" onclick="changePage(-1)" disabled><span class="iconify" data-icon="mdi-chevron-left"></span></button>
                <input id="page-select-input" name="page" type="number" value="1" onchange="changePage(0)" />
                <button id="next-page" class="secondary" onclick="changePage(1)" disabled><span class="iconify" data-icon="mdi-chevron-right"></span></button>
            </div>
        </div>
    </section>

    <!-- Modal edit -->
    <dialog id="dialog-edit">
        <article>
            <header>
                <button class="dialog-close" aria-label="Close" rel="prev"></button>
                <h2><?= lang($edit) ?></h2>
            </header>
            <form id="edit-search-form">
                <?php foreach ($fields as $key => $field): ?>
                    <?php if (is_array($field['type'])): ?>
                        <label>
                            <?= lang($field['lib']) ?>
                            <select id="edit-<?= $key ?>" name="<?= $key ?>">
                                <option selected value=""></option>
                                <?php foreach ($field['type'] as $k => $val): ?>
                                    <?php if ($field['col']): ?>
                                        <option value="<?= $k ?>"><?= lang($val) ?></option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </label>
                    <?php else: ?>
                        <label>
                            <?= lang($field['lib']) ?>
                            <input id="edit-<?= $key ?>" type="<?= $field['type'] ?>" name="<?= $key ?>">
                        </label>
                    <?php endif ?>
                <?php endforeach ?>
            </form>
            <footer>
                <button class="secondary dialog-close"><?= lang('App.common.cancel') ?></button>
                <button class="dialog-close" onclick="editData()"><?= lang('App.common.save') ?></button>
            </footer>
        </article>
    </dialog>

    <!-- Modal delete -->
    <dialog id="dialog-delete">
        <article>
            <header>
                <button class="dialog-close" aria-label="Close" rel="prev"></button>
                <h2><?= lang('App.common.confirmDelete') ?></h2>
            </header>
            <p><?= lang('App.common.messageDelete') ?></p>
            <footer>
                <button class="secondary dialog-close"><?= lang('App.common.cancel') ?></button>
                <button class="dialog-close" onclick="deleteDate()"><?= lang('App.common.confirm') ?></button>
            </footer>
        </article>
    </dialog>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script>
        const api = '<?= $api ?>';
        const fields = JSON.parse('<?= json_encode($fields) ?>');
        const mode = '<?= $mode ?>';
        const lang = {
            edit: '<?= lang('App.common.edit') ?>',
            del: '<?= lang('App.common.delete') ?>',
            errorAjax: '<?= lang('App.common.errorAjax') ?>',
            createSuccess: '<?= lang('App.common.createSuccess') ?>',
            updateSuccess: '<?= lang('App.common.updateSuccess') ?>',
            deleteSuccess: '<?= lang('App.common.deleteSuccess') ?>',
        }
    </script>
    <script src="/js/crud.js"></script>
<?= $this->endSection() ?>
