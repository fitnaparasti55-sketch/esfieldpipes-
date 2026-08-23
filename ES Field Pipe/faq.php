<?php
/**
 * Frequently Asked Questions (FAQ) - Esfield Pipe
 */
$pageTitle = "Frequently Asked Questions & Technical FAQs";
require_once __DIR__ . '/includes/header.php';

$db = get_db();
$faqs = $db->query("SELECT * FROM `faqs` WHERE `status` = 'active' ORDER BY `display_order` ASC")->fetchAll();
?>

<div class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="text-center mb-5">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 rounded-pill mb-2">TECHNICAL KNOWLEDGE BASE</span>
                    <h2 class="fw-black text-dark">Frequently Asked Questions</h2>
                    <p class="text-muted mx-auto" style="max-width: 650px;">Everything you need to know regarding IS 16098 Part 2 standards, ring stiffness calculations, installation trench guidelines, and freight dispatch.</p>
                </div>

                <div class="accordion border-0 shadow-sm rounded-4 overflow-hidden mb-5" id="faqAccordion">
                    <?php foreach($faqs as $idx => $faq): ?>
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header" id="faqHeading_<?= $faq['id'] ?>">
                            <button class="accordion-button <?= $idx === 0 ? '' : 'collapsed' ?> fw-bold text-dark py-3.5 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse_<?= $faq['id'] ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                                <span class="badge bg-primary bg-opacity-10 text-primary me-3"><?= $idx + 1 ?></span>
                                <?= htmlspecialchars($faq['question']) ?>
                            </button>
                        </h2>
                        <div id="faqCollapse_<?= $faq['id'] ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body px-4 py-3 text-muted leading-relaxed" style="line-height: 1.7;">
                                <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Still have questions banner -->
                <div class="card border-0 shadow-sm p-4 p-md-5 text-center bg-white rounded-4">
                    <h4 class="fw-black text-dark mb-2">Have specific tender requirements or custom project questions?</h4>
                    <p class="text-muted mb-4">Our senior infrastructure technical consultants are ready to assist with sizing simulations and lab certificates.</p>
                    <div>
                        <a href="<?= BASE_URL ?>contact.php" class="btn btn-primary px-4 py-2.5 fw-bold rounded-3">
                            <i class="fa-solid fa-headset me-2"></i> Contact Technical Desk
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
