
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

