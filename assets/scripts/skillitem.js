const displayColumns = [
    { key: 'servicefunction', label: 'servicefunction' },
    { key: 'code', label: 'code' },
    { key: 'name', label: 'name' },
    { key: 'sort_order', label: 'sort order' },
];

$(document).ready(function () {
    if ($('#home_input').length > 0) { loadData() }
});

// Incremented on every load so a stale async render can't write into a
// newer view (e.g. list items appearing inside the table after a quick toggle).
var renderToken = 0;

function loadData() {
    $('#results').html('<div class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</div>');
        var n_servicefunction = $('.search').find('[name=servicefunction]').val();

    const search = $('input[name="search"]').val();
    const ps = pageState.get('page_size', '10');
    const ob = $('#order_filter').val();
    var uri = site + "/get-skillitem-records";

    $.ajax({
        url: uri,
        type: "POST",
        data: {
            search: search,
            page: current_page,
            order_by: ob,
            page_size: ps,
            servicefunction: n_servicefunction
        },
        success: function (response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            displayResults(data.records, data.pagination);
            $('#total_records_label').html(data.pagination.total_records)
        },
        error: function () {
            $('#results').html('<div class="empty-state"><i class="fas fa-triangle-exclamation"></i><p>Could not load records. Please try again.</p></div>');
        }
    });
}

function getColumnValue(item, key) {
    const value = item[key];
    if (value && typeof value === 'object' && 'name' in value) {
        return value.name;
    }
    return value ?? '';
}

function appendText($element, value) {
    $element.append(document.createTextNode(value ?? ''));
}

function buildDetailsUrl(item) {
    return site + "/view-" + table + "/" + item.iD;
}

function buildListItem(item, number) {
    const $item = $('<div class="record-list-item fade-in"></div>');
    const $details = $('<p class="fw-light mb-2"></p>');

    $('<h5></h5>').text(number + ". " + (item.name || 'Record #' + item.iD)).appendTo($item);
    $('<span class="fw-normal"></span>').text('Status:').appendTo($details);
    appendText($details, ' ' + (item.status?.name || ''));

    displayColumns.forEach(function (column) {
        appendText($details, '  |  ');
        $('<span class="fw-normal text-black"></span>').text(column.label + ':').appendTo($details);
        appendText($details, ' ' + getColumnValue(item, column.key));
    });

    $item.append($details);
    $('<a class="btn btn-sm table-button"><span>View Details</span></a>')
        .attr('href', buildDetailsUrl(item))
        .appendTo($item);

    return $item;
}

function buildTable() {
    const $table = $('<table class="table table-borderless table-hover align-middle"></table>');
    const $headRow = $('<tr></tr>');

    $('<th></th>').appendTo($headRow);
    displayColumns.forEach(function (column) {
        $('<th></th>').text(column.label).appendTo($headRow);
    });
    $('<th>Status</th><th></th>').appendTo($headRow);

    $table.append($('<thead class="text-capitalize"></thead>').append($headRow));
    $table.append('<tbody></tbody>');
    return $table;
}

function buildTableRow(item, number) {
    const $row = $('<tr></tr>');

    $('<td></td>').text(number).appendTo($row);
    displayColumns.forEach(function (column) {
        $('<td></td>').text(getColumnValue(item, column.key)).appendTo($row);
    });
    $('<td></td>').text(item.status?.name || '').appendTo($row);
    $('<td class="text-end"></td>').append(
        $('<a class="btn btn-sm table-button"><span>View</span></a>').attr('href', buildDetailsUrl(item))
    ).appendTo($row);

    return $row;
}

function displayResults(records, pagination) {
    const token = ++renderToken;
    $('#results').html('');

    if (records.length > 0) {
        const pageSize = parseInt(pageState.get('page_size', '10'));
        const start = (pagination.current_page - 1) * pageSize + 1;

        if (currentView === 'list') {
            records.forEach((item, index) => {
                const $item = buildListItem(item, index + start);
                // Small stagger for a subtle cascade; capped so long pages
                // stay fast, and token-guarded so a view switch mid-render
                // can't append stale items into the new view.
                setTimeout(() => {
                    if (token !== renderToken) return;
                    $('#results').append($item);
                    requestAnimationFrame(() => { $item.addClass('show'); });
                }, Math.min(index * 30, 600));
            });
        } else {
            const $table = buildTable();
            $('#results').append($table);
            let $tbody = $('#results table tbody');
            records.forEach((item, index) => {
                $tbody.append(buildTableRow(item, index + start));
            });
        }

        generate_pagination_list(pagination.total_pages, pagination.total_records)

    } else {
        $('#results').html(
            '<div class="empty-state">' +
            '<i class="fas fa-inbox"></i>' +
            '<p>No records found.</p>' +
            '<a class="btn create-btn" href="' + site + '/new-' + table + '"><i class="fa fa-plus me-1"></i> Create the first one</a>' +
            '</div>'
        );
        generate_pagination_list(0, 0)
    }
}
