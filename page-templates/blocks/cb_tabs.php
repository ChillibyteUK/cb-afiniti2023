<?php
$classes = $block['className'] ?? null;
$colours = array(
    '#accf83' => 'green',
    '#575b8a' => 'purple',
    '#ed9025' => 'orange',
    '#474747' => 'grey',
);
?>
<!-- tabs -->
<!-- tab content  -->
<div class="container-xl responsive-tabs <?=$classes?>">
    <ul class="row nav nav-tabs" role="tablist">
    <?php
    $active = 'active';
    $c = 0;
    while (have_rows('tabs')) {
        the_row();
        ?>
        <li class="col-lg-3 nav-item">
            <a id="tab-<?=$c?>" href="#pane-<?=$c?>" class="bg--<?=$colours[get_sub_field('background')]?> px-3 py-3 nav-link <?=$active?>" data-bs-toggle="tab"
                role="tab">
                <span class="fs-4 fw-bold text-uppercase"><?=get_sub_field('tab_title')?></span><br />
                <span class="fs-5"><?=get_sub_field('tab_subtitle')?></span>
            </a>
        </li>
        <?php
        $active = '';
        $c++;
    }
    ?>
    </ul>


    <div id="content" class="tab-content mb-4" role="tablist">
        <?php
        $c = 0;
        $active = 'active';
        $show = 'show';
        while (have_rows('tabs')) {
            the_row();
            $link = get_sub_field('cta_link') ?: null;
            ?>
        <div id="pane-<?=$c?>" class="card tab-pane <?=$show?> <?=$active?>" role="tabpanel" aria-labelledby="tab-<?=$c?>">

            <div class="card-header bg--<?=$colours[get_sub_field('background')]?>" role="tab" id="heading-<?=$c?>">
                <h5 class="mb-0">
                    <a data-bs-toggle="collapse" href="#collapse-<?=$c?>" aria-expanded="true"
                        aria-controls="collapse-<?=$c?>"><strong><?=get_sub_field('tab_title')?></strong> <?=get_sub_field('tab_subtitle')?></a>
                </h5>
            </div>

            <div id="collapse-<?=$c?>" class="collapse <?=$show?>" data-bs-parent="#content" role="tabpanel"
                aria-labelledby="heading-<?=$c?>">
                <div class="card-body">
                    <div class="bg--<?=$colours[get_sub_field('background')]?> breakout-lg">
                        <div class="container py-4">
                            <div class="row">
                                <div class="col-12 col-lg-9 order-2 order-lg-1 text-white">
                                    <?php
                                    if (get_sub_field('alt_content_title')) {
                                        ?>
                                    <h2 class="py-3"><strong><?=get_sub_field('alt_content_title')?></strong></h2>
                                        <?php
                                    }
                                    else {
                                        ?>
                                    <h2 class="py-3"><strong><?=get_sub_field('tab_title')?></strong></h2>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 order-2 order-lg-1 text-white div-235">
                                    <?=get_sub_field('content')?>
                                </div>
                                <div class="col-12 col-lg-4 order-1 order-lg-2 text-center">
                                    
                                    <img src="<?=wp_get_attachment_image_url(get_sub_field('image'),'full')?>"
                                        style="left:50px;" class="bc-img img-fluid wow animated fadeIn">
                                    <div class="border-circle-bg"></div>
                                </div>
                            </div>
                            <?php
                            if ($link) {
                                ?>
                            <div class="only-mobile pt-4">
                                <a href="<?=$link['url']?>"
                                    class="text-white">Find
                                    out more <span class="arrow arrow-white"></span></a>
                            </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    if ($link) {
                        ?>
                    <div class="container no-mobile">
                        <div class="row">
                            <div class="col-12 col-lg-4 offset-lg-4 text-center halfcircle-container">
                                <div class="div-rounded ss-halfcircle halfcircle-<?=$colours[get_sub_field('background')]?> text-white">
                                    <div class="halfcircle-content font-weight-bold">
                                        <a
                                            href="<?=$link['url']?>">
                                            <div class="text-white">Find out more</div><span
                                                class="arrow arrow-block arrow-white mt-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
            <?php
            $c++;
            $active = '';
            $show = '';
        }
        ?>
    </div>
</div>