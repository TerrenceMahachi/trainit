@extends('layouts.main')

<?php global $siteConfig; ?>
<main class="trainit-page">
    <section class="trainit-page-head">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow">Contact</p>
            <h1>Let&apos;s get your next request moving.</h1>
            <p class="trainit-lead">Reach out about HR, finance, IT, marketing, hosting, a new website, cloud services, or support for an existing system.</p>
        </div>
    </section>
    <section class="trainit-section trainit-white">
        <div class="trainit-wrap trainit-split">
            <div>
                <h2>Send us a direct message.</h2>
                <p class="mb-4">Tell us what you are looking to achieve, and our team will get back to you promptly.</p>

                <div id="contactAlert" class="d-none alert mb-3"></div>

                <form id="contactForm" class="trainit-contact-form">
                    <div class="mb-3">
                        <label for="contact_name" class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="contact_name" name="name" class="form-control" placeholder="Your Name" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label for="contact_email" class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="contact_email" name="email" class="form-control" placeholder="you@company.co.zw" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="contact_phone" class="form-label fw-bold">Phone / WhatsApp</label>
                            <input type="tel" id="contact_phone" name="phone" class="form-control" placeholder="+263 77 000 0000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="contact_subject" class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                        <select id="contact_subject" name="subject" class="form-select">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Cloud & Systems Support">Cloud &amp; Systems Support</option>
                            <option value="Talent Roster & Placements">Talent Roster &amp; Placements</option>
                            <option value="Managed Services & Billing">Managed Services &amp; Billing</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contact_message" class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                        <textarea id="contact_message" name="message" class="form-control" rows="4" placeholder="How can we assist your organization?" required></textarea>
                    </div>
                    <button type="submit" id="contactSubmitBtn" class="trainit-button trainit-button-primary w-100 justify-content-center">
                        <i class="fa fa-paper-plane me-2"></i> Send Message
                    </button>
                </form>
            </div>

            <aside class="trainit-contact-box">
                <h3>Direct contact channels</h3>
                <div class="trainit-contact-row">
                    <a href="mailto:hello@trainit.co.zw"><i class="fa fa-envelope me-2 text-primary"></i> hello@trainit.co.zw</a>
                    <a href="mailto:sales@trainit.co.zw"><i class="fa fa-tag me-2 text-warning"></i> sales@trainit.co.zw</a>
                    <a href="mailto:jobs@trainit.co.zw"><i class="fa fa-briefcase me-2 text-success"></i> jobs@trainit.co.zw</a>
                    <a href="mailto:admin@trainit.co.zw"><i class="fa fa-shield-alt me-2 text-purple"></i> admin@trainit.co.zw</a>
                    <span><i class="fa fa-map-marker-alt me-2 text-danger"></i> Harare, Zimbabwe</span>
                </div>
            </aside>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('contactForm');
        var alertBox = document.getElementById('contactAlert');
        var btn = document.getElementById('contactSubmitBtn');

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Sending...';

            var formData = new FormData(form);

            fetch('<?= $siteConfig->siteUrl ?>/contact', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                alertBox.classList.remove('d-none', 'alert-danger', 'alert-success');
                if (data.status == 1) {
                    alertBox.classList.add('alert-success');
                    alertBox.innerHTML = '<i class="fa fa-check-circle me-1"></i> ' + data.msg;
                    form.reset();
                } else {
                    alertBox.classList.add('alert-danger');
                    alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> ' + (data.msg || 'Could not send message.');
                }
            })
            .catch(function(err) {
                alertBox.classList.remove('d-none', 'alert-success');
                alertBox.classList.add('alert-danger');
                alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Error sending message. Please email hello@trainit.co.zw directly.';
            })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-paper-plane me-2"></i> Send Message';
            });
        });
    });
    </script>
</main>
