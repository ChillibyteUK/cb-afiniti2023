<?php
$classes = $block['className'] ?? null;
?>
<!-- pillar_nav -->
<section class="pillar_nav <?=$classes?>">
    <div class="container-xl">
        <div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
        <div class="d-none d-lg-block col-6"></div>
        <div class="row mb-5 g-4">
            <div class="col-12 col-lg-3">
                <div class="row no-mobile">
                    <div class="col-6"></div>
                    <div class="col-6 border-dash-top-left h-30px"></div>
                </div>
                <a
                    href="<?=get_field('shaping_link', 'options')?>">
                    <div class="bg--green-500 pillar-shadow">
                        <div class="text-white px-3 py-3 border-bottom border-white">
                            <span class="fs-4 fw-bold">SHAPING</span><br>
                            <span class="fs-5">for change</span>
                        </div>
                        <div class="text-white px-3 py-3">
                            <div class="pillar-text">
                                <?=get_field('shaping_intro', 'options')?>
                            </div>
                            <div class="fw-bold pt-4 pb-2">
                                <div class="text-white anim-arrow--slide">Find out
                                    more <span class="arrow me-3"></span></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-lg-3">
                <div class="row no-mobile">
                    <div class="col-6 border-dash-top"></div>
                    <div class="col-6 border-dash-top-left h-30px"></div>
                </div>
                <a
                    href="<?=get_field('readiness_link', 'options')?>">
                    <div class="bg--purple-500 pillar-shadow">
                        <div class="text-white px-3 py-3 border-bottom border-white">
                            <span class="fs-4 fw-bold">READINESS </span><br>
                            <span class="fs-5">for change</span>
                        </div>
                        <div class="text-white px-3 py-3">
                            <div class="pillar-text">
                                <?=get_field('readiness_intro', 'options')?>
                            </div>
                            <div class="fw-bold pt-4 pb-2">
                                <div class="text-white anim-arrow--slide">Find
                                    out more <span class="arrow me-3"></span></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-lg-3">
                <div class="row no-mobile">
                    <div class="col-6 border-dash-top-right h-30px"></div>
                    <div class="col-6 border-dash-top"></div>
                </div>
                <a
                    href="<?=get_field('delivering_link', 'options')?>">
                    <div class="bg--orange-500 pillar-shadow">
                        <div class="text-white px-3 py-3 border-bottom border-white">
                            <span class="fs-4 fw-bold">DELIVERING</span><br>
                            <span class="fs-5">successful change</span>
                        </div>
                        <div class="text-white px-3 py-3">
                            <div class="pillar-text">
                                <?=get_field('delivering_intro', 'options')?>
                            </div>
                            <div class="fw-bold pt-4 pb-2">
                                <div class="text-white anim-arrow--slide">Find
                                    out more <span class="arrow me-3"></span></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-lg-3">
                <div class="row no-mobile">
                    <div class="col-6 border-dash-top-right h-30px"></div>
                    <div class="col-6"></div>
                </div>
                <a
                    href="<?=get_field('embedding_link', 'options')?>">
                    <div class="bg--grey-500 pillar-shadow">
                        <div class="text-white px-3 py-3 border-bottom border-white">
                            <span class="fs-4 fw-bold">EMBEDDING</span><br>
                            <span class="fs-5">change</span>
                        </div>
                        <div class="text-white px-3 py-3">
                            <div class="pillar-text">
                                <?=get_field('embedding_intro', 'options')?>
                            </div>
                            <div class="fw-bold pt-4 pb-2">
                                <div class="text-white anim-arrow--slide">Find out
                                    more <span class="arrow me-3"></span></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>