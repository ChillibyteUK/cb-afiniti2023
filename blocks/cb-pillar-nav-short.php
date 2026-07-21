<?php
$url = get_permalink();

$classes = $block['className'] ?? null;

?>
<!-- pillar_nav_short -->
<div class="container-xl mb-5 pillar_nav <?=$classes?>">
    <div class="d-none d-lg-block col-6 border-dash-right h-90px"></div>
    <div class="row">
        <?php

if (!preg_match('/shaping/', $url)) {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6"></div>
                <div class="col-6 border-dash-top-left h-30px"></div>
            </div>
            <a href="/business-change-management/shaping-for-change/">
                <div class="bg--green-500 pillar-shadow text--white p-3">
                    <div class="pillar-title">
                        <span class="fs-4 fw-bold">SHAPING</span>
                        <br class="no-mobile">
                        <span class="fs-5">for change</span>
                    </div>
                    <div class="fw-bold pt-4 pb-2">
                        <div class="text--white anim-arrow--slide text-end text-lg-start">Find out more
                            <span class="arrow mr-3"></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php
} else {
    ?>
        <div class="col-12 col-md-3">
            <div class="row no-mobile">
                <div class="col-6"></div>
                <div class="col-6 border-dash-top-left h-30px"></div>
            </div>
            <div class="border--green-500 text--green pillar-active p-3">
                <span class="fs-4 fw-bold">SHAPING</span>
                <br />
                <span class="fs-5">for change</span>
                <div class="pin"></div>
            </div>
        </div>
        <?php
}

//readiness
if (!preg_match('/readiness/', $url)) {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6 border-dash-top"></div>
                <div class="col-6 border-dash-top-left h-30px"></div>
            </div>
            <a href="/business-change-management/readiness-for-change/">
                <div class="bg--purple-500 pillar-shadow text--white p-3">
                    <span class="fs-4 fw-bold">READINESS</span>
                    <br />
                    <span class="fs-5">for change</span>
                    <div class="fw-bold pt-4 pb-2">
                        <div class="text--white anim-arrow--slide text-end text-lg-start">Find out more
                            <span class=" arrow arrow-white mr-3"></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php
} else {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6 border-dash-top"></div>
                <div class="col-6 border-dash-top-left h-30px"></div>
            </div>
            <div class="border--purple-500 text--purple pillar-active p-3">
                <span class="fs-4 fw-bold">READINESS</span>
                <br />
                <span class="fs-5">for change</span>
                <div class="pin"></div>
            </div>
        </div>
        <?php
}
    
//delivering
if (!preg_match('/delivering/', $url)) {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6 border-dash-top-right h-30px"></div>
                <div class="col-6 border-dash-top"></div>
            </div>
            <a href="/business-change-management/delivering-successful-change/">
                <div class="bg--orange-500 pillar-shadow text--white p-3">
                    <span class="fs-4 fw-bold">DELIVERING</span>
                    <br />
                    <span class="fs-5">successful change</span>
                    <div class="fw-bold pt-4 pb-2">
                        <div class="text--white anim-arrow--slide text-end text-lg-start">Find out more
                            <span class=" arrow arrow-white mr-3"></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php
} else {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6 border-dash-top-right h-30px"></div>
                <div class="col-6 border-dash-top"></div>
            </div>
            <div class="border--orange-500 text--orange pillar-active p-3">
                <span class="fs-4 fw-bold">DELIVERING</span>
                <br />
                <span class="fs-5">successful change</span>
                <div class="pin"></div>
            </div>
        </div>
        <?php
}

//embedding
if (!preg_match('/embedding/', $url)) {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6 border-dash-top-right h-30px"></div>
                <div class="col-6"></div>
            </div>
            <a href="/business-change-management/embedding-change/">
                <div class="bg--grey-500 pillar-shadow text--white p-3">
                    <span class="fs-4 fw-bold">EMBEDDING</span>
                    <br />
                    <span class="fs-5">change</span>
                    <div class="fw-bold pt-4 pb-2">
                        <div class="text--white anim-arrow--slide text-end text-lg-start">Find out more
                            <span class=" arrow arrow-white mr-3"></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php
} else {
    ?>
        <div class="col-12 col-lg-3">
            <div class="row no-mobile">
                <div class="col-6 border-dash-top-right h-30px"></div>
                <div class="col-6"></div>
            </div>
            <div class="border--grey-500 text--grey pillar-active p-3">
                <span class="fs-4 fw-bold">EMBEDDING</span>
                <br />
                <span class="fs-5">change</span>
                <div class="pin"></div>
            </div>
        </div>
        <?php
}

?>
    </div>
</div>