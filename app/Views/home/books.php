<?= $this->extend('layout/crud') ?>

<?= $this->section('detail') ?>
    <!-- Description -->
    <section>
        <h4><?= lang('App.manage.books.description') ?></h4>
        <p id="description"></p>
    </section>
    <hr/>
    <!-- Authors -->
    <section>
        <h4><?= lang('App.manage.books.authors') ?></h4>
        <div class="overflow-auto">
            <table>
                <thead>
                    <tr>
                        <td><?= lang('App.manage.authors.username') ?></td>
                        <td><?= lang('App.manage.authors.role') ?></td>
                    </tr>
                </thead>
                <tbody id="author-table-content">
                    <tr id="detail-no-data-row" class="none">
                        <td class="center" colspan="2"><?= lang('App.common.noData') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
<?= $this->endSection() ?>

<?= $this->section('detail-script') ?>
    <script>
        async function loadPopupDetail(id) {
            const noDataRow = document.getElementById('detail-no-data-row');
            // Reset
            noDataRow.classList.add('none');
            document.querySelectorAll('.detail-author-data-loaded')?.forEach(elt => elt.remove());
            // Show description
            document.getElementById('description').innerHTML = (data[id]['description'] != null && data[id]['description'].trim() !== '') ? '<blockquote>' + data[id]['description'] + '</blockquote>' : '<i><?= lang('App.manage.books.noDescription') ?></i>';
            // Get authors
            const result = await callGet('/api/books/' + id + '/authors');
            if (!result.success) {
                noDataRow.classList.remove('none');
                console.error(result);
                return;
            }
            // Generate table
            let html = '';
            for (const author of result.data) {
                html += '<tr class="detail-author-data-loaded">';
                html += '<td>' + author['username'] + '</td>';
                html += '<td>' + author['role'] + '</td>';
                html += '</tr>';
            }
            const authorTable = document.getElementById('author-table-content');
            authorTable.innerHTML = authorTable.innerHTML + html;
        }
    </script>
<?= $this->endSection() ?>