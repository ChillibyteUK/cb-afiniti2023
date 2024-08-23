<?php
$id = $block['anchor'] ?? null;
?>
<section class="faq_block py-5" id="<?=$id?>">
    <div class="container-xl">
        <?php
        if (get_field('faq_title') ?? null) {
            ?>
        <h2><?=get_field('faq_title')?></h2>
            <?php
        }

        $accordion = random_str(5);
    
        echo '<div class="faq_block__inner">';
        
        echo '<div itemscope="" itemtype="https://schema.org/FAQPage" id="accordion' .  $accordion . '" class="accordion accordion-flush">';
        $counter = 0;
        $show = '';
        $collapsed = 'collapsed';
        while (have_rows('faqs')) {
            the_row();
            $ac = $accordion . '_' . $counter;
            ?>
            <div itemscope="" itemprop="mainEntity" itemtype="https://schema.org/Question" class="accordion-item">
                <div class="accordion-head accordion-collapse <?=$collapsed?>" itemprop="name" data-bs-toggle="collapse" id="heading_<?=$ac?>" data-bs-target="#c<?=$ac?>" role="button" aria-expanded="true" aria-controls="c<?=$ac?>">
                    <div class="pb-1"><?=get_sub_field('question')?></div>
                </div>
                <div class="collapse <?=$show?>" itemscope="" itemprop="acceptedAnswer" itemtype="https://schema.org/Answer" id="c<?=$ac?>" aria-labelledby="heading_<?=$ac?>" data-bs-parent="#accordion<?=$accordion?>">
                    <div itemprop="text" class="faq__answer mb-4">
                        <p><?=get_sub_field('answer')?></p>
                    </div>
                </div>
            </div>
            <?php
            $counter++;
            $show = '';
            $collapsed = 'collapsed';
        }
        echo '</div>';
        ?>
    </div>
</section>