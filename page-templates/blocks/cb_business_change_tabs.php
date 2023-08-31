<?php
$shaping_link = get_field('shaping_link');
$readiness_link = get_field('readiness_link');
$delivering_link = get_field('delivering_link');
$embedding_link = get_field('embedding_link');

$classes = $block['className'] ?? null;

?>
<!-- business_change_tabs -->
<!-- tab content  -->
<div class="container-xl responsive-tabs <?=$classes?>">
    <ul class="row nav nav-tabs" role="tablist">
        <li class="col-lg-3 nav-item">
            <a id="shaping-tab" href="#pane-shaping" class="bg--green px-3 py-3 nav-link active" data-bs-toggle="tab"
                role="tab">
                <span class="fs-4 fw-bold text-uppercase">Shaping</span><br />
                <span class="fs-5">for change</span>
            </a>
        </li>
        <li class="col-lg-3 nav-item">
            <a id="readiness-tab" href="#pane-readiness" class="bg--purple px-3 py-3 nav-link" data-bs-toggle="tab"
                role="tab">
                <span class="fs-4 fw-bold text-uppercase">Readiness</span><br />
                <span class="fs-5">for change</span>
            </a>
        </li>
        <li class="col-lg-3 nav-item">
            <a id="delivering-tab" href="#pane-delivering" class="bg--orange px-3 py-3 nav-link" data-bs-toggle="tab"
                role="tab">
                <span class="fs-4 fw-bold text-uppercase">Delivering</span><br />
                <span class="fs-5">successful change</span>
            </a>
        </li>
        <li class="col-lg-3 nav-item">
            <a id="embedding-tab" href="#pane-embedding" class="bg--grey px-3 py-3 nav-link" data-bs-toggle="tab"
                role="tab">
                <span class="fs-4 fw-bold text-uppercase">Embedding</span><br />
                <span class="fs-5">change</span>
            </a>
        </li>
    </ul>


    <div id="content" class="tab-content mb-4" role="tablist">
        <!-- shaping -->
        <div id="pane-shaping" class="card tab-pane show active" role="tabpanel" aria-labelledby="shaping-tab">

            <div class="card-header bg--green" role="tab" id="heading-A">
                <h5 class="mb-0">
                    <a data-bs-toggle="collapse" href="#collapse-A" aria-expanded="true"
                        aria-controls="collapse-A"><strong>Shaping</strong> for change</a>
                </h5>
            </div>

            <div id="collapse-A" class="collapse show" data-bs-parent="#content" role="tabpanel"
                aria-labelledby="heading-A">
                <div class="card-body">
                    <div class="bg--green breakout-lg">
                        <div class="container py-4">
                            <div class="row">
                                <div
                                    class="d-none d-lg-block offset-lg-2 col-lg-1 border-dash-bottom-left border-white h-30px">
                                </div>
                                <div class="d-none d-lg-block col-lg-6 border-dash-bottom border-white h-30px"></div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-9 order-2 order-lg-1 text-white">
                                    <h2 class="py-3"><strong>Shaping</strong> for change</h2>
                                </div>
                                <div
                                    class="d-none d-lg-block col-lg-1 order-lg-2 border-dash-top-right border-white h-30px mt-minus-2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 order-2 order-lg-1 text-white div-235">
                                    <?=get_field('shaping_content')?>
                                </div>
                                <div class="col-12 col-lg-4 order-1 order-lg-2 text-center">
                                    <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Flag_bearer_full.png"
                                        style="left:50px;" class="bc-img img-fluid wow animated fadeIn">
                                    <div class="border-circle-bg"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-8 border-dash-bottom-right border-white h-30px">
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-6 border-dash-top-left border-white mt-minus-2 h-30px">
                                </div>
                            </div>
                            <div class="only-mobile pt-4">
                                <a href="<?=$shaping_link['url']?>"
                                    class="text-white">Find
                                    out more <span class="arrow arrow-white"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="container no-mobile">
                        <div class="row">
                            <div class="col-12 col-lg-4 offset-lg-4 text-center halfcircle-container">
                                <div class="div-rounded ss-halfcircle halfcircle-green text-white">
                                    <div class="halfcircle-content font-weight-bold">
                                        <a
                                            href="<?=$shaping_link['url']?>">
                                            <div class="text-white">Find out more</div><span
                                                class="arrow arrow-block arrow-white mt-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- READINESS -->
        <div id="pane-readiness" class="card tab-pane" role="tabpanel" aria-labelledby="readiness-tab">

            <div class="card-header bg--purple" role="tab" id="heading-B">
                <h5 class="mb-0">
                    <a class="collapsed" data-bs-toggle="collapse" href="#collapse-B" aria-expanded="false"
                        aria-controls="collapse-B"><strong>Readiness</strong> for change</a>
                </h5>
            </div>

            <div id="collapse-B" class="collapse" data-bs-parent="#content" role="tabpanel" aria-labelledby="heading-B">
                <div class="card-body">
                    <div class="bg--purple breakout-lg">
                        <div class="container py-4">
                            <div class="row">
                                <div
                                    class="d-none d-lg-block offset-lg-4 col-lg-1 border-dash-bottom-left border-white h-30px">
                                </div>
                                <div class="d-none d-lg-block col-lg-4 border-dash-bottom border-white h-30px"></div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-9 order-2 order-lg-1 text-white">
                                    <h2 class="py-3"><strong>Readiness</strong> for change</h2>
                                </div>
                                <div
                                    class="d-none d-lg-block col-lg-1 order-lg-2 border-dash-top-right border-white h-30px mt-minus-2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 order-2 order-lg-1 text-white div-235">
                                    <?=get_field('readiness_content')?>
                                </div>
                                <div class="col-12 col-lg-4 order-1 order-lg-2 text-center">
                                    <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Sextant_Small.png"
                                        class="bc-img img-fluid animated fadeIn">
                                    <div class="border-circle-bg"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-8 border-dash-bottom-right border-white h-30px">
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-6 border-dash-top-left border-white mt-minus-2 h-30px">
                                </div>
                            </div>
                            <div class="only-mobile pt-4">
                                <a class="text-white"
                                    href="<?=$readiness_link['url']?>">Find
                                    out more <span class="arrow arrow-white"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="container no-mobile">
                        <div class="row">
                            <div class="col-12 col-lg-4 offset-lg-4 text-center halfcircle-container">
                                <div class="div-rounded ss-halfcircle halfcircle-purple text-white">
                                    <div class="halfcircle-content font-weight-bold">
                                        <a
                                            href="<?=$readiness_link['url']?>">
                                            <div class="text-white">Find out more</div><span
                                                class="arrow arrow-block arrow-white mt-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- DELIVERING -->
        <div id="pane-delivering" class="card tab-pane" role="tabpanel" aria-labelledby="delivering-tab">

            <div class="card-header bg--orange" role="tab" id="heading-C">
                <h5 class="mb-0">
                    <a class="collapsed" data-bs-toggle="collapse" href="#collapse-C" aria-expanded="false"
                        aria-controls="collapse-C"><strong>Delivering</strong> successful change</a>
                </h5>
            </div>

            <div id="collapse-C" class="collapse" role="tabpanel" data-bs-parent="#content" aria-labelledby="heading-C">
                <div class="card-body">
                    <div class="bg--orange breakout-lg">
                        <div class="container py-4">
                            <div class="row">
                                <div
                                    class="d-none d-lg-block offset-lg-7 col-lg-1 border-dash-bottom-left border-white h-30px">
                                </div>
                                <div class="d-none d-lg-block col-lg-1 border-dash-bottom border-white h-30px"></div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-9 order-2 order-lg-1 text-white">
                                    <h2 class="py-3"><strong>Delivering</strong> successful change</h2>
                                </div>
                                <div
                                    class="d-none d-lg-block col-lg-1 order-lg-2 border-dash-top-right border-white h-30px mt-minus-2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 order-2 order-lg-1 text-white div-235">
                                    <?=get_field('delivering_content')?>
                                </div>
                                <div class="col-12 col-lg-4 order-1 order-lg-2 text-center">
                                    <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/Applauding.png"
                                        style="left:-50px;" class="bc-img img-fluid animated fadeIn">
                                    <div class="border-circle-bg"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-8 border-dash-bottom-right border-white h-30px">
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-6 border-dash-top-left border-white mt-minus-2 h-30px">
                                </div>
                            </div>
                            <div class="only-mobile pt-4">
                                <a class="text-white"
                                    href="<?=$delivering_link['url']?>">Find
                                    out more <span class="arrow arrow-white"></span></a>
                            </div>
                        </div>
                    </div>
                    <div class="container no-mobile">
                        <div class="row">
                            <div class="col-12 col-lg-4 offset-lg-4 text-center halfcircle-container">
                                <div class="div-rounded ss-halfcircle halfcircle-orange text-white">
                                    <div class="halfcircle-content font-weight-bold">
                                        <a
                                            href="<?=$delivering_link['url']?>">
                                            <div class="text-white">Find out more</div><span
                                                class="arrow arrow-block arrow-white mt-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- EMBEDDING -->
        <div id="pane-embedding" class="card tab-pane" role="tabpanel" aria-labelledby="embedding-tab">

            <div class="card-header bg--grey" role="tab" id="heading-D">
                <h5 class="mb-0">
                    <a class="collapsed" data-bs-toggle="collapse" href="#collapse-D" aria-expanded="false"
                        aria-controls="collapse-D"><strong>Embedding</strong> change</a>
                </h5>
            </div>

            <div id="collapse-D" class="collapse" role="tabpanel" data-bs-parent="#content" aria-labelledby="heading-D">
                <div class="card-body">
                    <div class="bg--grey breakout-lg">
                        <div class="container py-4">
                            <div class="row">
                                <div
                                    class="d-none d-lg-block offset-lg-9 col-lg-1 border-dash-right border-white h-30px">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-9 order-2 order-lg-1 text-white">
                                    <h2 class="py-3"><strong>Embedding</strong> change</h2>
                                </div>
                                <div
                                    class="d-none d-lg-block col-lg-1 order-lg-2 border-dash-right border-white h-30px mt-minus-2">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-8 order-2 order-lg-1 text-white div-235">
                                    <?=get_field('embedding_content')?>
                                </div>
                                <div class="col-12 col-lg-4 order-1 order-lg-2 text-center">
                                    <img src="<?=get_stylesheet_directory_uri()?>/img/illustrations/drawing_pin.png"
                                        class="bc-img img-fluid animated fadeIn">
                                    <div class="border-circle-bg"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-8 border-dash-bottom-right border-white h-30px">
                                </div>
                            </div>
                            <div class="row">
                                <div
                                    class="d-none d-lg-block col-lg-2 offset-lg-6 border-dash-top-left border-white mt-minus-2 h-30px">
                                </div>
                            </div>
                            <div class="only-mobile pt-4">
                                <a class="text-white"
                                    href="<?=$embedding_link['url']?>">Find
                                    out more <span class="arrow arrow-white"></span></a>
                            </div>

                        </div>
                    </div>
                    <div class="container no-mobile">
                        <div class="row">
                            <div class="col-12 col-lg-4 offset-lg-4 text-center halfcircle-container">
                                <div class="div-rounded ss-halfcircle halfcircle-grey text-white">
                                    <div class="halfcircle-content font-weight-bold">
                                        <a
                                            href="<?=$embedding_link['url']?>">
                                            <div class="text-white">Find out more</div><span
                                                class="arrow arrow-block arrow-white mt-2"></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>