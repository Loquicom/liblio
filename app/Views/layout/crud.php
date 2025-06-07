<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?><?= lang($title) ?><?= $this->endSection() ?>

<?= $this->section('style') ?>
    <link rel="stylesheet" href="/css/crud.css" />
    <link rel="stylesheet" href="/css/autocomplete.css" />
<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <?= view_cell('HeaderCell', ['title' => $title, 'redirect' => auth()->loggedIn() ? 'manage' : '/', 'return' => $return ?? null]) ?>
<?= $this->endSection() ?>

<?= $this->section('main') ?>
    <!-- Search -->
    <section id="search">
        <div class="center">
            <div id="simple-search" role="search">
                <input id="simple-search-input" name="search" type="search" placeholder="<?= lang('App.common.search') ?>" onkeyup="sendSearch(event)" />
                <button id="search-btn" onclick="loadData()"><span class="iconify" data-icon="mdi-send"></span></button>
            </div>
        </div>
        <details id="advanced-search">
            <summary onclick="setSearchMode()"><?= lang('App.common.advancedSearch') ?></summary>
            <div id="advanced-search-form">
                <?php $i = 0 ?>
                <?php $autocomplete = [] ?>
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
                    <?php elseif (str_starts_with($field['type'], 'autocomplete')): ?>
                        <?php $autocomplete['advanced-search-' . $key] = $field ?>
                        <label class="autocomplete-label">
                            <?= lang($field['lib']) ?> <a id="advanced-search-<?= $key ?>-load-all" data-tooltip="<?= lang('App.common.loadAll') ?>" data-placement="top" onclick="loadAllData('advanced-search-<?= $key ?>')"><span class="iconify" data-icon="mdi-sync"></span></a>
                            <input id="advanced-search-<?= $key ?>-autocomplete" type="text" name="autocomplete-advanced-search-<?= $key ?>" onchange="resetIfEmpty('advanced-search-<?= $key ?>')">
                        </label>
                        <input id="advanced-search-<?= $key ?>" type="hidden" name="<?= $key ?>" class="none">
                    <?php else: ?>
                        <label>
                            <?= lang($field['lib']) ?>
                            <input id="advanced-search-<?= $key ?>" type="<?= $field['type'] ?>" name="<?= $key ?>" onkeyup="sendSearch(event)">
                        </label>
                    <?php endif ?>
                    <?php if ($i++ % 3 == 2): ?>
                        </fieldset>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if (($i-1) % 3 != 2): ?>
                    </fieldset>
                <?php endif ?>
            </div>
        </details>
    </section>
    <!-- Sort & Add -->
    <section>
        <div class="grid">
            <div>
                <select id="sort-select" name="sort" aria-label="Sort columns" onchange="loadData()">
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
        <small id="total-indicator" class="hide"><?= lang('App.common.total') ?> : <span id="current-value">?</span>/<span id="total-value">?</span></small>
        <div class="overflow-auto">
            <table class="striped">
                <thead>
                <tr>
                    <?php foreach ($fields as $key => $field): ?>
                        <?php if ($field['col']): ?>
                            <th scope="col"><?= lang($field['lib']) ?></th>
                        <?php endif ?>
                    <?php endforeach ?>
                    <?php if ($mode === 'edit' || $detail !== 'none'): ?>
                        <th class="center" scope="col"><?= lang('App.common.action') ?></th>
                    <?php endif ?>
                </tr>
                </thead>
                <tbody id="table-content">
                <tr id="loading-row">
                    <td class="center" colspan="<?= $mode === 'edit' || $detail !== 'none'  ? count($fields) + 1 : count($fields) ?>" aria-busy="true"><?= lang('App.common.loading') ?></td>
                </tr>
                <tr id="no-data-row" class="none">
                    <td class="center" colspan="<?= $mode === 'edit' || $detail !== 'none' ? count($fields) + 1 : count($fields) ?>"><?= lang('App.common.noData') ?></td>
                </tr>
                </tbody>
            </table>
        </div>
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
                            <select id="edit-<?= $key ?>" name="<?= $key ?>" <?= isset($field['disabled']) ? 'disabled' : '' ?>>
                                <option selected value=""></option>
                                <?php foreach ($field['type'] as $k => $val): ?>
                                    <?php if ($field['col']): ?>
                                        <option value="<?= $k ?>"><?= lang($val) ?></option>
                                    <?php endif ?>
                                <?php endforeach ?>
                            </select>
                        </label>
                    <?php elseif (str_starts_with($field['type'], 'autocomplete')): ?>
                        <?php $autocomplete['edit-' . $key] = $field ?>
                        <label class="autocomplete-label">
                            <?= lang($field['lib']) ?> <a id="edit-<?= $key ?>-load-all" data-tooltip="<?= lang('App.common.loadAll') ?>" data-placement="top" onclick="loadAllData('edit-<?= $key ?>')"><span class="iconify" data-icon="mdi-sync"></span></a>
                            <input id="edit-<?= $key ?>-autocomplete" type="text" name="autocomplete-edit-<?= $key ?>" onchange="resetIfEmpty('edit-<?= $key ?>')">
                        </label>
                        <input id="edit-<?= $key ?>" type="hidden" name="<?= $key ?>" class="none">
                    <?php else: ?>
                        <label>
                            <?= lang($field['lib']) ?>
                            <input id="edit-<?= $key ?>" type="<?= $field['type'] ?>" name="<?= $key ?>" <?= isset($field['disabled']) ? 'disabled' : '' ?>>
                            <?php if(isset($field['helper'])): ?>
                                <small><?= is_string($field['helper']) ? lang($field['helper']) : lang($field['helper']['lib'], $field['helper']['val']) ?></small>
                            <?php endif ?>
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
                <button class="dialog-close" onclick="deleteData()"><?= lang('App.common.confirm') ?></button>
            </footer>
        </article>
    </dialog>

    <!-- Modal detail -->
    <dialog id="dialog-detail">
        <article>
            <header>
                <button class="dialog-close" aria-label="Close" rel="prev"></button>
                <h2><?= lang('App.common.detail') ?></h2>
            </header>
            <?= $this->renderSection('detail') ?>
            <footer>
                <button class="dialog-close"><?= lang('App.common.close') ?></button>
            </footer>
        </article>
    </dialog>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
    <script src="/js/autocomplete.min.js"></script>
    <script>
        const api = '<?= $api ?>';
        const fields = JSON.parse('<?= str_replace("'", "\\'", json_encode($fields)) ?>');
        const mode = '<?= $mode ?>';
        const detail = '<?= $detail ?>';
        const lang = {
            edit: '<?= lang('App.common.edit') ?>',
            del: '<?= lang('App.common.delete') ?>',
            detail: '<?= lang('App.common.detail') ?>',
            errorAjax: '<?= lang('App.common.errorAjax') ?>',
            createSuccess: '<?= lang('App.common.createSuccess') ?>',
            updateSuccess: '<?= lang('App.common.updateSuccess') ?>',
            deleteSuccess: '<?= lang('App.common.deleteSuccess') ?>',
        }
        const url = {
            api: '<?= $api ?>'
        }

        const autocomplete = {};
        <?php foreach ($autocomplete as $key => $field): ?>
            autocomplete['<?= $key ?>'] = new autoComplete({
                name: 'autocomplete-<?= $key ?>',
                selector: '#<?= $key ?>-autocomplete',
                data: {
                    src: async(query) => {
                        let data = [];
                        const url = '<?= explode(':', $field['type'])[2] ?>';
                        if (autocomplete['<?= $key ?>'].threshold === 0 && query?.trim() === '') {
                            data = await callGet(url, {number: 500});
                            data = data.data.values;
                        } else if (query != null) {
                            data = await callGet(url + '/search/' + query, {number: 5});
                            data = data.data;
                        }
                        return data;
                    },
                    keys: ['<?= explode(':', $field['type'])[1] ?>'],
                    cache: false
                },
                threshold: 3,
                debounce: 500,
                resultsList: {
                    maxResults: 5
                },
                resultItem: {
                    highlight: true
                },
                events: {
                    input: {
                        selection: (event) => {
                            const selection = event.detail.selection.value;
                            autocomplete['<?= $key ?>'].input.value = selection[autocomplete['<?= $key ?>'].data.keys[0]];
                            document.getElementById('<?= $key ?>').value = selection['id'];
                            <?php if(!str_starts_with($key, 'edit')): ?>
                                loadData();
                            <?php endif; ?>
                        },
                        focus() {
                            autocomplete['<?= $key ?>'].start();
                        }
                    }
                }
            });
        <?php endforeach; ?>
    </script>
    <script src="/js/crud.js"></script>
    <?= $this->renderSection('detail-script') ?>
<?= $this->endSection() ?>
