@extends('layouts.main')


<!-- Navigation -->
<?php
use function APP\Helpers\formatDate;
global $siteConfig;
?>
<div>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h3>User Accounts</h3>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">

          
            <form method="GET" action="<?= $siteConfig->siteUrl; ?>/users" class="mb-4">
                <div class="d-flex">
                    <div class="input-group input-group-lg">
                        <input type="search" name="search" class="form-control" placeholder="Search users"
                            value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">

                        <button class="btn search-btn" type="submit">Search</button>
                    </div>
                    <a class="btn create-btn rounded-2 p-3 px-5  ms-3"
                        href="<?= $siteConfig->siteUrl; ?>/users/create">
                        Create </a>

                </div>
            </form>
            <br>
            <?php if (!empty($data['records'])): ?>

                <div class="mb-5">
                    <?php

                    foreach ($data['records']['records'] as $item): ?>
                        <div class=" mb-4 border rounded-2 py-4 ps-5 bg-white shadow-sm">
                            <h5><?= $item->name; ?></h5>
                            <p class="fw-light">
                              <span class="fw-normal">  Role: </span><?= $item->role()->name; ?>
                              &nbsp;|&nbsp;  <span class="fw-normal"> Email:</span> <?= $item->email; ?>
                              <?php if(count($item->login())>0){ ?>
                              &nbsp;|&nbsp;  <span class="fw-normal"> Login Status:</span> <?= $item->login()[0]->status()->name; ?>
                              <?php } ?>
                            </p>

                            <a class="btn  button1 " href="<?= $siteConfig->siteUrl; ?>/users/view/<?= $item->iD; ?>">
                                <span class="">View details</span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br>
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination rounded-pill p-3 justify-content-center">
                        <?php
                        $pagination = $data['records']['pagination'];
                        $rows_per_page = $_COOKIE['page_size'] ?? $pagination['rows_per_page']; // Default to current rows per page
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
                            global  $rows_per_page;
                            $queryParams['page_size'] = $rows_per_page; // Pass page size in URL
                            return http_build_query($queryParams);
                        }
                        // Calculate current range of records
                        $startRecord = ($pagination['current_page'] - 1) * $pagination['rows_per_page'] + 1;
                        $endRecord = min($pagination['current_page'] * $pagination['rows_per_page'], $pagination['total_records']);


                        ?>

                        <?php if ($pagination['current_page'] > 1): ?>
                            <!-- <li class="page-item bg-5">
                                <a class="page-link"
                                    href="<?= $currentUrl; ?>?<?= buildPaginationUrl($pagination['current_page'] - 1); ?>"
                                    aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li> -->
                        <?php endif; ?>

                        <!-- Dropdown list for page selection -->
                        <li class="page-item dropdown bg-transparent">
                            <a class="page-link dropdown-toggle bg-nav rounded-pill p-3 px-5 acc-alt-link" href="#"
                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                Showing records <?= $startRecord; ?> to <?= $endRecord; ?> of
                                <?= $pagination['total_records']; ?>

                            </a>
                            <ul class="dropdown-menu w-100 text-center nav-dropdown" aria-labelledby="dropdownMenuButton">
                                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                    <?php
                                    $startRecord = (($i - 1) * $pagination['rows_per_page']) + 1;
                                    $endRecord = min($i * $pagination['rows_per_page'], $pagination['total_records']);
                                    ?>
                                    <li><a class="dropdown-item" href="<?= $currentUrl; ?>?<?= buildPaginationUrl($i); ?>">
                                            Show records <?= $startRecord; ?> - <?= $endRecord; ?> of
                                            <?= $pagination['total_records']; ?>
                                        </a></li>
                                <?php endfor; ?>
                            </ul>
                        </li>

                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                           <!--  <li class="page-item">
                                <a class="page-link"
                                    href="<?= $currentUrl; ?>?<?= buildPaginationUrl($pagination['current_page'] + 1); ?>"
                                    aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li> -->
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="d-flex justify-content-center mt-3 w-100">
                    
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="page_size" id="pageSize10" value="10"
                            <?= ($_GET['page_size'] ?? '10') == '10' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="pageSize10">10</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="page_size" id="pageSize25" value="25"
                            <?= ($_GET['page_size'] ?? '10') == '25' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="pageSize25">25</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="page_size" id="pageSize50" value="50"
                            <?= ($_GET['page_size'] ?? '10') == '50' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="pageSize50">50</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="page_size" id="pageSize100" value="100"
                            <?= ($_GET['page_size'] ?? '10') == '100' ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="pageSize100">100</label>
                    </div>
                    
                </div>
            <?php else: ?>
                <p>No records found.</p>
            <?php endif; ?>
        </div>
    </section>


</div>