@extends('layouts.main')

<?php
global $siteConfig;
$statuses = $data['statuses'];
$tracks = $data['tracks'];
?>

<script>
    var table = "admin-roster";
    var site = "<?= $siteConfig->siteUrl; ?>";
</script>

<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Admin Dashboard</a></p>
                <h1>Talent Vetting & Roster Pipeline</h1>
                <p class="portal-dashboard-intro">Review incoming Apprentice and Associate applications, perform 100-point scoring, and manage admission to the talent network.</p>
            </div>
            <div class="portal-account-summary">
                <span>Total Applications</span>
                <strong><span id="header_total_records">...</span> Candidates</strong>
                <small>Stage 1 & 2 Review Console</small>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 12px;">
                
                <!-- Toolbar: search on left -->
                <div class="row justify-content-between align-items-center g-2 mb-3">
                    <div class="col-12 col-md-6">
                        <form id="searchForm" onsubmit="current_page = 1; loadData(); return false;">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                <input type="search" name="search" id="roster_search_input" class="form-control border-start-0" placeholder="Search applicant name, email, or city...">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-12 col-md-auto text-md-end">
                        <span class="text-muted small me-2">Total Candidates:</span>
                        <span class="badge bg-primary fs-6" id="total_records_label">0</span>
                    </div>
                </div>

                <!-- Filters row: view toggle, track filter, status filter, sort order -->
                <div class="row g-2 align-items-center mb-3">
                    <div class="col-auto">
                        <div class="view-toggle" id="viewToggle" role="group" aria-label="Switch view">
                            <span class="listView p-2" title="List view"><i class="fas fa-list"></i></span>
                            <span class="tableView p-2" title="Table view"><i class="fas fa-table"></i></span>
                        </div>
                    </div>

                    <div class="col-auto">
                        <select class="form-select form-select-sm" id="track_filter" aria-label="Filter Track">
                            <option value="0">All Application Tracks</option>
                            <?php foreach ($tracks as $tr): ?>
                                <option value="<?= $tr->iD; ?>"><?= htmlspecialchars($tr->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-auto">
                        <select class="form-select form-select-sm" id="status_filter" aria-label="Filter Status">
                            <option value="0">All Application Statuses</option>
                            <?php foreach ($statuses as $st): ?>
                                <option value="<?= $st->iD; ?>"><?= htmlspecialchars($st->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-auto ms-auto">
                        <select class="form-select form-select-sm" id="order_filter" aria-label="Sort order">
                            <option value="reg_date DESC" selected>Newest Submissions First</option>
                            <option value="reg_date ASC">Oldest Submissions First</option>
                            <option value="legal_name ASC">Name A - Z</option>
                            <option value="legal_name DESC">Name Z - A</option>
                            <option value="iD DESC">Application ID (High - Low)</option>
                        </select>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Results container -->
                <div id="results" class="my-3">
                    <div class="text-center text-muted py-5">
                        <span class="spinner-border spinner-border-sm me-2"></span> Loading pipeline candidates...
                    </div>
                </div>

                <!-- Boilerplate Standard AJAX Pagination Include -->
                <div id="pagination-controls" class="d-flex justify-content-center mt-4"></div>
                <?php include($siteConfig->assetsLoc . '/nav/ajax-pagination.php'); ?>

            </div>

        </div>
    </section>
</main>

<input type="hidden" id="home_input" value="1">

<script>
$(document).ready(function () {
    $('#track_filter').on('change', function () {
        current_page = 1;
        pageState.set('current_page', current_page);
        loadData();
    });

    $('#status_filter').on('change', function () {
        current_page = 1;
        pageState.set('current_page', current_page);
        loadData();
    });

    if ($('#home_input').length > 0) {
        loadData();
    }
});

function loadData() {
    $('#results').html('<div class="text-center text-muted py-5"><span class="spinner-border text-primary me-2"></span>Loading pipeline candidates...</div>');
    
    const search = $('#roster_search_input').val();
    const ps = pageState.get('page_size', '10');
    const ob = $('#order_filter').val();
    const track = $('#track_filter').val();
    const status = $('#status_filter').val();
    const uri = site + "/get-admin-roster-records";

    $.ajax({
        url: uri,
        type: "POST",
        dataType: "json",
        data: {
            search: search,
            page: current_page,
            order_by: ob,
            page_size: ps,
            track: track,
            status: status
        },
        success: function (response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            if (data.status === 1) {
                displayResults(data.records, data.pagination);
                $('#total_records_label').html(data.pagination.total_records);
                $('#header_total_records').html(data.pagination.total_records);
            } else {
                $('#results').html('<div class="alert alert-warning text-center py-4">' + (data.msg || 'No candidates found.') + '</div>');
            }
        },
        error: function () {
            $('#results').html('<div class="alert alert-danger text-center py-4"><i class="fa fa-exclamation-triangle me-2"></i>Could not load candidate records. Please try again.</div>');
        }
    });
}

function displayResults(records, pagination) {
    generate_pagination_list(pagination.total_pages, pagination.total_records);

    if (!records || records.length === 0) {
        $('#results').html('<div class="text-center py-5 text-muted"><i class="fa fa-inbox fa-3x mb-3 d-block"></i><h5>No candidates found</h5><p class="small">Try adjusting your filters or search terms.</p></div>');
        return;
    }

    if (currentView === 'table') {
        const $table = $('<div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>#ID</th><th>Applicant Name</th><th>Track</th><th>Primary Function</th><th>City / Province</th><th>Status</th><th>Score</th><th>Submitted Date</th><th class="text-end">Actions</th></tr></thead><tbody></tbody></table></div>');
        const $tbody = $table.find('tbody');

        records.forEach(function (app) {
            let badgeClass = 'bg-secondary';
            if (app.status_code === 'submitted') badgeClass = 'bg-primary';
            else if (app.status_code === 'screened') badgeClass = 'bg-info text-dark';
            else if (app.status_code === 'interviewed') badgeClass = 'bg-warning text-dark';
            else if (app.status_code === 'on_roster') badgeClass = 'bg-success';
            else if (app.status_code === 'rejected') badgeClass = 'bg-danger';

            const scoreDisplay = (app.total_score !== null && app.total_score > 0)
                ? '<strong>' + parseFloat(app.total_score).toFixed(1) + '/100</strong>'
                : '<span class="text-muted small">Not scored</span>';

            const trackBadgeClass = (app.track_code === 'apprentice') ? 'bg-success' : 'bg-primary';

            const $row = $('<tr></tr>');
            $row.html(`
                <td class="fw-bold">#${app.iD}</td>
                <td>
                    <div class="fw-bold text-dark">${escapeHtml(app.legal_name)}</div>
                    <small class="text-muted">${escapeHtml(app.email)}</small>
                </td>
                <td><span class="badge ${trackBadgeClass}">${escapeHtml(app.track_name)}</span></td>
                <td>${escapeHtml(app.primary_function)}</td>
                <td>${escapeHtml(app.city)}${app.province ? ', ' + escapeHtml(app.province) : ''}</td>
                <td><span class="badge ${badgeClass}">${escapeHtml(app.status_name)}</span></td>
                <td>${scoreDisplay}</td>
                <td><small class="text-muted">${app.reg_date}</small></td>
                <td class="text-end">
                    <a href="${site}/admin/roster/review?id=${app.iD}" class="btn btn-sm btn-outline-primary fw-semibold">
                        <i class="fa fa-gavel me-1"></i> Review & Score
                    </a>
                </td>
            `);
            $tbody.append($row);
        });

        $('#results').html($table);
    } else {
        const $listContainer = $('<div class="row g-3"></div>');

        records.forEach(function (app) {
            let badgeClass = 'bg-secondary';
            if (app.status_code === 'submitted') badgeClass = 'bg-primary';
            else if (app.status_code === 'screened') badgeClass = 'bg-info text-dark';
            else if (app.status_code === 'interviewed') badgeClass = 'bg-warning text-dark';
            else if (app.status_code === 'on_roster') badgeClass = 'bg-success';
            else if (app.status_code === 'rejected') badgeClass = 'bg-danger';

            const trackBadgeClass = (app.track_code === 'apprentice') ? 'bg-success' : 'bg-primary';
            const scoreDisplay = (app.total_score !== null && app.total_score > 0)
                ? '<span class="badge bg-dark fs-6">' + parseFloat(app.total_score).toFixed(1) + ' / 100 PTS</span>'
                : '<span class="badge bg-light text-muted border">Not Scored</span>';

            const $card = $(`
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm" style="border-radius: 10px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge ${trackBadgeClass}">${escapeHtml(app.track_name)}</span>
                                    <span class="badge ${badgeClass}">${escapeHtml(app.status_name)}</span>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark">#${app.iD} ${escapeHtml(app.legal_name)}</h6>
                                <p class="text-muted small mb-2"><i class="fa fa-envelope me-1"></i> ${escapeHtml(app.email)}</p>
                                <p class="text-muted small mb-2"><i class="fa fa-briefcase me-1"></i> ${escapeHtml(app.primary_function)}</p>
                                <p class="text-muted small mb-2"><i class="fa fa-map-marker-alt me-1"></i> ${escapeHtml(app.city)}${app.province ? ', ' + escapeHtml(app.province) : ''}</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                <div>${scoreDisplay}</div>
                                <a href="${site}/admin/roster/review?id=${app.iD}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-gavel me-1"></i> Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            $listContainer.append($card);
        });

        $('#results').html($listContainer);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}
</script>
