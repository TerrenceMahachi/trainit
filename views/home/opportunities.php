@extends('layouts.main')

<?php global $siteConfig; ?>
<main class="trainit-page opportunity-page">
    <section class="opportunity-hero">
        <div class="opportunity-orb opportunity-orb-one" aria-hidden="true"></div>
        <div class="opportunity-orb opportunity-orb-two" aria-hidden="true"></div>
        <div class="trainit-wrap opportunity-hero-grid">
            <div class="opportunity-hero-copy opportunity-reveal">
                <p class="opportunity-kicker"><span></span> The Trainit talent network</p>
                <h1>Build experience. Stay ready for the right opportunity.</h1>
                <p class="opportunity-intro">Trainit brings capable people into real client work. Apprentices develop practical experience with guidance, while experienced associates are called in when opportunities match their expertise and availability.</p>
                <div class="trainit-actions">
                    <a class="trainit-button opportunity-button" href="<?= $siteConfig->siteUrl ?>/register">
                        Register on the platform <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a class="trainit-button opportunity-button-ghost" href="#choose-your-path">Explore the two paths</a>
                </div>
                <p class="opportunity-note"><i class="fa fa-info-circle" aria-hidden="true"></i> Everyone starts with the same Trainit account, then requests the professional profile that fits them.</p>
            </div>
            <figure class="opportunity-hero-media opportunity-reveal" data-delay="120">
                <img src="<?= $siteConfig->assetsUrl ?>/images/opportunities/apprentices-work-experience.webp"
                    alt="Apprentices collaborating on a real project with an experienced mentor"
                    width="1600" height="852" fetchpriority="high">
                <figcaption>
                    <strong>Experience that counts</strong>
                    <span>Real briefs, practical feedback and professional standards.</span>
                </figcaption>
            </figure>
        </div>
    </section>

    <section class="opportunity-section" id="choose-your-path">
        <div class="trainit-wrap">
            <div class="opportunity-section-heading opportunity-reveal">
                <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Choose your path</p>
                <h2>Different stages. One standard of meaningful work.</h2>
                <p>Whether you are building your foundation or bringing established expertise, Trainit aims to create clear, well-matched ways to contribute.</p>
            </div>

            <article class="opportunity-feature opportunity-feature-apprentice opportunity-reveal">
                <div class="opportunity-feature-image">
                    <img src="<?= $siteConfig->assetsUrl ?>/images/opportunities/apprentices-work-experience.webp"
                        alt="Young professionals receiving guidance while working together"
                        width="1600" height="852" loading="lazy">
                    <span class="opportunity-image-label">Apprentice path</span>
                </div>
                <div class="opportunity-feature-copy">
                    <span class="opportunity-number">01</span>
                    <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Learn by contributing</p>
                    <h2>Turn knowledge into work experience.</h2>
                    <p>Apprentices join to strengthen the bridge between study, training and professional delivery. When a suitable supervised assignment is available, they can contribute to real client outcomes while learning how quality work is scoped, communicated and completed.</p>
                    <ul class="opportunity-benefits">
                        <li><i class="fa fa-briefcase" aria-hidden="true"></i><span><strong>Practical exposure</strong>Work on real requests across HR, finance, IT and marketing.</span></li>
                        <li><i class="fa fa-compass" aria-hidden="true"></i><span><strong>Guidance and feedback</strong>Learn from experienced professionals and clear review standards.</span></li>
                        <li><i class="fa fa-chart-line" aria-hidden="true"></i><span><strong>Career evidence</strong>Build credible experience, better work habits and a stronger professional story.</span></li>
                    </ul>
                    <a class="opportunity-text-link" href="<?= $siteConfig->siteUrl ?>/register">Start as an apprentice <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </article>

            <article class="opportunity-feature opportunity-feature-associate opportunity-reveal">
                <div class="opportunity-feature-copy">
                    <span class="opportunity-number">02</span>
                    <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Expertise when it matters</p>
                    <h2>Be considered when the right opportunity arises.</h2>
                    <p>Associates are experienced professionals who want to make their expertise available without becoming permanent employees. Trainit maintains a vetted network and reaches out when a client request aligns with an associate&apos;s skills, availability, location and level of experience.</p>
                    <ul class="opportunity-benefits">
                        <li><i class="fa fa-user-check" aria-hidden="true"></i><span><strong>A trusted professional profile</strong>Present verified qualifications, experience and specialist areas.</span></li>
                        <li><i class="fa fa-bell" aria-hidden="true"></i><span><strong>Relevant opportunity calls</strong>Hear from Trainit when suitable client work becomes available.</span></li>
                        <li><i class="fa fa-sync-alt" aria-hidden="true"></i><span><strong>Flexible participation</strong>Consider each assignment according to your current capacity and interest.</span></li>
                    </ul>
                    <a class="opportunity-text-link" href="<?= $siteConfig->siteUrl ?>/register">Join as an associate <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="opportunity-feature-image">
                    <img src="<?= $siteConfig->assetsUrl ?>/images/opportunities/associates-on-demand.webp"
                        alt="An experienced associate consulting with a client team"
                        width="1600" height="854" loading="lazy">
                    <span class="opportunity-image-label">Associate path</span>
                </div>
            </article>
        </div>
    </section>

    <section class="opportunity-process">
        <div class="trainit-wrap">
            <div class="opportunity-section-heading opportunity-reveal">
                <p class="opportunity-kicker"><span></span> How joining works</p>
                <h2>From registration to a well-matched assignment.</h2>
            </div>
            <div class="opportunity-steps">
                <article class="opportunity-step opportunity-reveal">
                    <span>01</span>
                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                    <h3>Create your account</h3>
                    <p>Register once on Trainit using the shared account process.</p>
                </article>
                <article class="opportunity-step opportunity-reveal" data-delay="80">
                    <span>02</span>
                    <i class="fa fa-file-signature" aria-hidden="true"></i>
                    <h3>Request your profile</h3>
                    <p>Choose Apprentice or Associate and provide your qualifications, experience, availability and expertise.</p>
                </article>
                <article class="opportunity-step opportunity-reveal" data-delay="160">
                    <span>03</span>
                    <i class="fa fa-shield-alt" aria-hidden="true"></i>
                    <h3>Complete vetting</h3>
                    <p>Trainit reviews the information and may request evidence, references or a conversation.</p>
                </article>
                <article class="opportunity-step opportunity-reveal" data-delay="240">
                    <span>04</span>
                    <i class="fa fa-hands-helping" aria-hidden="true"></i>
                    <h3>Consider opportunities</h3>
                    <p>When a suitable request arises, Trainit discusses the assignment, expectations, timing and rate with you.</p>
                </article>
            </div>
            <p class="opportunity-disclaimer opportunity-reveal">Registration and vetting place you in the Trainit talent network but do not guarantee an assignment. Opportunities depend on client demand, suitability, availability and successful vetting.</p>
        </div>
    </section>

    <section class="opportunity-cta">
        <div class="trainit-wrap opportunity-cta-inner opportunity-reveal">
            <div>
                <p class="opportunity-kicker"><span></span> Take the first step</p>
                <h2>Bring your potential or experience into the network.</h2>
                <p>Create your Trainit account today. It is the starting point for an Apprentice or Associate profile and future opportunity matching.</p>
            </div>
            <a class="trainit-button opportunity-button opportunity-button-large" href="<?= $siteConfig->siteUrl ?>/register">
                Register now <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </section>
</main>
<script src="<?= $siteConfig->assetsUrl ?>/scripts/opportunities.js?v=<?= _ASSET_VERSION ?>"></script>
