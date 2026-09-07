<?php
/** Shared modals + loading/toast scaffolding used across the app. */
global $siteConfig;
?>
<div id="loadingOverlay" class="d-none">
    <div class="overlay-content">
        <span class="spinner-border text-light" role="status"></span>
        <span class="text-light">Processing...</span>
    </div>
</div>

<div class="modal fade" id="view_option_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-light text-black">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

<div class="modal bg-lime fade" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title text-muted text-center my-5">Please confirm that you want to logout.</h5><br>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-default rounded-4 shadow-sm px-4 py-2 text-warning"
                    data-bs-dismiss="modal"><i class="fas fa-times mr-2"></i> Cancel</button>
                <a class="btn btn-outline-default rounded-4 shadow-sm px-4 py-2 text-success" id="btn_logout"
                    onclick="logout()"><i class="fas fa-save mr-2"></i> Confirm Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="modal bg-white" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="confirm-message"></p>
            </div>
            <div class="modal-footer">
                <button id="confirm-action-btn-yes" type="button" class="btn btn-success">Yes</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<div class="modal fade bg-white" id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-body border-0 position-relative">
                <div id="showModalOverlay"
                    class="position-absolute top-0 start-0 w-100 h-100 bg-white d-flex justify-content-center align-items-center"
                    style="z-index: 1050; background-color: rgba(255, 255, 255, 0.8);">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <iframe id="showFormIframe" src="" class="show-form-iframe"></iframe>
            </div>
        </div>
    </div>
</div>
