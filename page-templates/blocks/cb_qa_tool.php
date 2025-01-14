<?php
$classes = $block['className'] ?? null;
?>
<!-- qa_tool -->
<div class="container-xl pb-5 <?=$classes?>">
    <ul class="nav tab-buttons mb-3" id="qaTabs" role="tablist">
        <?php
        $faq_items = [];
// $active = 'active';
$active = '';
// $selected = 'true';
$selected = 'false';
while (have_rows('section')) {
    the_row();
    $s = acf_slugify(get_sub_field('section_title'));
    $theme = strtolower(get_sub_field('theme'));
    $parts = preg_split('/-/', $theme);
    $theme = $parts[0];

    // Collect FAQ data for schema
    $faq_items[] = [
        "@type" => "Question",
        "name" => get_sub_field('question'),
        "acceptedAnswer" => [
            "@type" => "Answer",
            "text" => get_sub_field('answer')
        ]
    ];
    ?>
        <li class="nav-item" role="presentation">
            <button
                class="tab-link <?=$active?> bg--<?=$theme?>-700"
                id="<?=$s?>-tab" data-bs-toggle="tab"
                data-bs-target="#<?=$s?>-tab-pane" type="button"
                role="tab" aria-controls="<?=$s?>-tab-pane"
                aria-selected="<?=$selected?>">
                <img src="<?=wp_get_attachment_image_url(get_sub_field('icon'), 'full')?>"
                    alt="">
                <p><?=get_sub_field('section_title')?>
                </p>
                </a>
        </li>
        <?php
    $active = '';
    $selected = 'false';
}
?>
    </ul>
    <div class="tab-content qa" id="myTabContent">

        <?php
// $state = 'show active';
$state = '';
$ti = 0;
$qq = 0;
while (have_rows('section')) {
    the_row();
    $s = acf_slugify(get_sub_field('section_title'));
    $theme = strtolower(get_sub_field('theme'));
    $parts = preg_split('/-/', $theme);
    $theme = $parts[0];

    ?>
        <div class="tab-pane fade <?=$state?>"
            id="<?=$s?>-tab-pane" role="tabpanel"
            aria-labelledby="<?=$s?>-tab"
            tabindex="<?=$ti?>">

            <div
                class="bg--<?=$theme?>-700 p-4 rounded qa__intro mb-4">
                <img src="<?=wp_get_attachment_image_url(get_sub_field('icon'), 'full')?>"
                    alt="">
                <div>
                    <h2 class="qa__section">
                        <?=get_sub_field('section_title')?>
                    </h2>
                    <p><?=get_sub_field('section_intro')?>
                    </p>
                </div>
            </div>
            <div class="accordion" id="accordion<?=$ti?>">
                <?php
    while (have_rows('questions')) {
        the_row();

        $question = get_sub_field('question') ?? null;
        $answer = get_sub_field('answer') ?? null;

        if (empty($question) || empty($answer)) {
            echo '<pre>' . "Missing FAQ data: " . json_encode([
                'section_title' => get_sub_field('section_title'),
                'question' => $question,
                'answer' => $answer
            ]) . '</pre>';
        }

        if (!empty($question) && !empty($answer)) {
            $faq_items[] = [
                "@type" => "Question",
                "name" => $question,
                "acceptedAnswer" => [
                    "@type" => "Answer",
                    "text" => $answer
                ]
            ];
        }
        ?>
                <div class="qa__question">
                    <button class="qa__button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse<?=$qq?>"
                        id="heading<?=$qq?>">
                        <?=get_sub_field('question')?>
                    </button>
                    <div id="collapse<?=$qq?>"
                        class="accordion-collapse collapse"
                        data-bs-parent="#accordion<?=$ti?>">
                        <div class="accordion-body" itemprop="text">
                            <div class="pb-4">
                                <?=get_sub_field('answer')?>
                            </div>
                            <?php
                    $related = get_sub_field('related');
        if ($related) {
            echo '<h4>Related Articles</h4>';
            echo '<div class="row">';
            foreach ($related as $r) {
                $img = get_the_post_thumbnail_url($r, 'large');
                ?>
                            <div class="insight insight--short col-12 col-lg-4 mb-4">
                                <a href="<?=get_the_permalink($r)?>">
                                    <div class="post-image-container">
                                        <div class="post-image mb-2"
                                            style="background-image:url('<?=$img?>')">
                                            <div class="img-overlay">
                                                <div class="middle"><span class="arrow arrow-block arrow-white"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="article-title mt-2">
                                        <?=get_the_title($r)?>
                                    </div>
                                    <div class="fw-bold py-2 arrow-link">
                                        <div class="anim-arrow--slide">Read more <span class="arrow arrow-green"></span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
            }
            echo '</div>';
        }
        ?>
                        </div>
                    </div>
                </div>
                <?php
        $qq++;
    }
    ?>
            </div>
        </div>
        <?php
    $state = '';
    $ti++;
}
?>
    </div>
</div>
<?php
if (!empty($faq_items)) {
    $faq_schema = [
        "@context" => "https://schema.org",
        "@type" => "FAQPage",
        "mainEntity" => $faq_items
    ];
    echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}