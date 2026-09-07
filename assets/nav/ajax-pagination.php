<nav class="nav w-100 justify-content-center mt-4">
    <ul class="pagination pagination-sm mx-auto align-items-center">
        <li class="page-item"><a class="page-link pager-btn" id="btn_previous" role="button"
                aria-label="Previous page">&laquo;</a></li>
        <li class="page-item px-2"><select id="page_list" class="form-select pager-select" aria-label="Page"></select>
        </li>
        <li class="page-item"><a class="page-link pager-btn" id="btn_next" role="button"
                aria-label="Next page">&raquo;</a></li>
    </ul>
</nav>
<div class="d-flex justify-content-center w-100">
    <select id="pageSizeDropdown" name="page_size" class="form-select w-auto bg-transparent" aria-label="Rows per page">
        <option value="10">10 rows per page</option>
        <option value="25">25 rows per page</option>
        <option value="50">50 rows per page</option>
        <option value="100">100 rows per page</option>
        <option value="250">250 rows per page</option>
    </select>
</div>
<script>
    /**
     * Per-page persistent state.
     *
     * Every key is namespaced by the module (`table` global set by the page's
     * header script, falling back to the URL path), so each listing remembers
     * ITS OWN view mode / page size / sort / page — switching to table view on
     * One listing no longer flips another listing to table view too.
     */
    var pageState = {
        ns: (typeof table !== 'undefined' && table) ? table : window.location.pathname,
        key: function (name) { return 'pg.' + this.ns + '.' + name; },
        get: function (name, fallback) {
            var v = localStorage.getItem(this.key(name));
            return (v === null || v === undefined) ? fallback : v;
        },
        set: function (name, value) { localStorage.setItem(this.key(name), value); }
    };

    var current_page = parseInt(pageState.get('current_page', '1')) || 1;
    var total_pages = 1;
    var currentView = pageState.get('viewMode', 'list');

    $(document).ready(function () {
        applyViewMode(currentView);

        $('#viewToggle .listView').on('click', function () { setViewMode('list'); });
        $('#viewToggle .tableView').on('click', function () { setViewMode('table'); });

        function setViewMode(view) {
            currentView = view;
            pageState.set('viewMode', view);
            applyViewMode(view);
            loadData();
        }

        // Restore per-page page size and sort order.
        $('#pageSizeDropdown').val(pageState.get('page_size', '10'));
        $('#order_filter').val(pageState.get('order_by', 'reg_date DESC'));

        $('#pageSizeDropdown').on('change', function () {
            pageState.set('page_size', parseInt($(this).val()));
            current_page = 1;
            pageState.set('current_page', current_page);
            loadData();
        });
        $('#order_filter').on('change', function () {
            pageState.set('order_by', $(this).val());
            current_page = 1;
            pageState.set('current_page', current_page);
            loadData();
        });
        $('.select').on('change', function () {
            current_page = 1;
            pageState.set('current_page', current_page);
            loadData();
        });
        $('#page_list').on('change', function () {
            current_page = parseInt($(this).val()) || 1;
            pageState.set('current_page', current_page);
            loadData();
        });
        $('#btn_next').on('click', next_page);
        $('#btn_previous').on('click', previous_page);
    });

    function applyViewMode(view) {
        $('#viewToggle .selected').removeClass('selected');
        if (view === 'list') {
            $('#viewToggle .listView').addClass('selected');
            $('#results').removeClass('table-view').addClass('list-view');
        } else {
            $('#viewToggle .tableView').addClass('selected');
            $('#results').removeClass('list-view').addClass('table-view');
        }
    }

    function next_page() {
        if (current_page < total_pages) {
            current_page += 1;
            pageState.set('current_page', current_page);
            loadData();
        }
    }
    function set_page(page) {
        current_page = page;
        pageState.set('current_page', current_page);
        loadData();
    }
    function previous_page() {
        if (current_page > 1) {
            current_page -= 1;
            pageState.set('current_page', current_page);
            loadData();
        }
    }

    function generate_pagination_list(tp, total_records) {
        total_pages = tp;

        // Clamp: a restored page can exceed the new total (records deleted,
        // filter narrowed). Snap back to the last real page and reload.
        if (total_pages > 0 && current_page > total_pages) {
            current_page = total_pages;
            pageState.set('current_page', current_page);
            loadData();
            return;
        }

        let optionsHtml = "", count = 1;
        const page_size = parseInt($('#pageSizeDropdown').val());

        if (total_records > 0 && total_pages > 0) {
            while (count <= total_pages) {
                optionsHtml += `<option value="${count}">${getPaginationText(total_records, page_size, count)}</option>`;
                count++;
            }
        } else {
            optionsHtml = '<option value="-1">No records</option>';
        }

        $("#page_list").html(optionsHtml);
        $("#page_list").val(current_page);
    }

    function getPaginationText(totalItemsCount, numberOfItemsPerPage, page) {
        const start = (page - 1) * numberOfItemsPerPage + 1;
        const end = Math.min(start + numberOfItemsPerPage - 1, totalItemsCount);
        return `${start} to ${end} of ${totalItemsCount}`;
    }
</script>
