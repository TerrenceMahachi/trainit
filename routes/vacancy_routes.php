<?php

use App\Controllers\VacancyController;

global $router;

// --- 1. Public Vacancy Endpoints (Recruitment Portal) ---

// Public vacancies directory listing (HTML or JSON)
$router->addRoute('GET', '/opportunities/vacancies', function () {
    (new VacancyController())->index();
    exit;
});

// View public vacancy detail and brief
$router->addRoute('GET', '/opportunities/vacancy/:slug', function ($slug) {
    (new VacancyController())->view($slug);
    exit;
});

// Candidate vacancy application submission (POST)
$router->addRoute('POST', '/opportunities/vacancy/:slug/apply', function ($slug) {
    (new VacancyController())->apply($slug);
    exit;
});


// --- 2. Admin & Staff Operations (Vacancy Management) ---

// Admin Vacancies Directory & Operational Counters
$router->addRoute('GET', '/admin/vacancies', function () {
    (new VacancyController())->adminIndex();
    exit;
});

// Create Vacancy Form
$router->addRoute('GET', '/admin/vacancies/create', function () {
    (new VacancyController())->adminCreate();
    exit;
});

// Store New Vacancy (POST)
$router->addRoute('POST', '/admin/vacancies/store', function () {
    (new VacancyController())->adminStore();
    exit;
});

// Edit Vacancy Form
$router->addRoute('GET', '/admin/vacancies/edit/:id', function ($id) {
    (new VacancyController())->adminEdit((int)$id);
    exit;
});

// Update Vacancy (POST)
$router->addRoute('POST', '/admin/vacancies/update', function () {
    (new VacancyController())->adminUpdate();
    exit;
});

// Quick Status Toggle (Published / Closed / Draft) (POST)
$router->addRoute('POST', '/admin/vacancies/toggle-status', function () {
    (new VacancyController())->adminToggleStatus();
    exit;
});

// View Applicant Dossiers for a Specific Vacancy
$router->addRoute('GET', '/admin/vacancies/applications/:id', function ($id) {
    (new VacancyController())->adminApplications((int)$id);
    exit;
});

// Update Applicant Status / Score / Notes / Interview (POST)
$router->addRoute('POST', '/admin/vacancies/application/update', function () {
    (new VacancyController())->adminUpdateApplication();
    exit;
});

// BRIDGE TO STAFF ONBOARDING: Appoint candidate as internal staff (POST)
$router->addRoute('POST', '/admin/vacancies/application/appoint', function () {
    (new VacancyController())->adminAppointStaff();
    exit;
});

// Secure CV Resume Download
$router->addRoute('GET', '/admin/vacancies/application/:id/cv', function ($id) {
    (new VacancyController())->downloadCv((int)$id);
    exit;
});
