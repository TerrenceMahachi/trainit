<nav aria-label="Page navigation">
    <ul class="pagination rounded-pill p-3 justify-content-center">
        <?php
        $pagination = $data['records']['pagination'];
        $rows_per_page =  $_COOKIE['page_size'] ?? $pagination['rows_per_page']; // Default to current rows per page
        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?'); // Get the current URL without query parameters
        $queryString = $_SERVER['QUERY_STRING'] ?? ''; // Get the query string, or an empty string if not set

        // Function to build the pagination URL
        function buildPaginationUrl($page)
        {
            global $queryString;
            $queryParams = [];
            if (!empty($queryString)) {
                parse_str($queryString, $queryParams);
            }
            $queryParams['page'] = $page;
            global $rows_per_page;
            $queryParams['page_size'] = $rows_per_page; // Pass page size in URL
            return http_build_query($queryParams);
        }

        // Calculate current range of records
        $startRecord = ($pagination['current_page'] - 1) * $pagination['rows_per_page'] + 1;
        $endRecord = min($pagination['current_page'] * $pagination['rows_per_page'], $pagination['total_records']);
        ?>

        <!-- Previous button (only show if current page > 1) -->
        <?php if ($pagination['current_page'] > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $currentUrl; ?>?<?= buildPaginationUrl($pagination['current_page'] - 1); ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Display records range -->
        <li class="page-item disabled">
            <a class="page-link">
                Showing records <?= $startRecord; ?> to <?= $endRecord; ?> of <?= $pagination['total_records']; ?>
            </a>
        </li>

        <!-- Next button (only show if current page < total pages) -->
        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
            <li class="page-item">
                <a class="page-link" href="<?= $currentUrl; ?>?<?= buildPaginationUrl($pagination['current_page'] + 1); ?>" aria-label="Next">
                    <span aria-hidden="true"> &raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>