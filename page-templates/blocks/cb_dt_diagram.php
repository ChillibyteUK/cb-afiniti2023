<section class="dt_diagram py-5">
    <div class="container-xl">
        <div class="row g-2">
            <div class="col-lg-6 order-lg-2">
                <?=wp_get_attachment_image(get_field('diagram'),'full',null)?>
            </div>
            <div class="col-lg-3 d-flex flex-column justify-content-around order-lg-1 gap-2">
                <div class="bg--grey-100 p-2">
                    <h3 class="text--dk-purple">Data</h3>
                    <p><?=get_field('data_description')?></p>
                    <p><em><?=get_field('data_strap')?></em></p>
                </div>
                <div class="bg--grey-100 p-2">
                    <h3 class="text--orange">Systems</h3>
                    <p><?=get_field('systems_description')?></p>
                    <p><em><?=get_field('systems_strap')?></em></p>
                </div>
            </div>
            <div class="col-lg-3 d-flex flex-column justify-content-around order-lg-3 gap-2">
                <div class="bg--grey-100 p-2">
                    <h3 class="text--green">Digital Strategy</h3>
                    <p><?=get_field('strategy_description')?></p>
                    <p><em><?=get_field('stragety_strap')?></em></p>
                </div>
                <div class="bg--grey-100 p-2">
                    <h3 class="text--purple">People</h3>
                    <p><?=get_field('people_description')?></p>
                    <p><em><?=get_field('people_strap')?></em></p>
                </div>
                <div class="bg--grey-100 p-2">
                    <h3 class="text--blue">Process</h3>
                    <p><?=get_field('process_description')?></p>
                    <p><em><?=get_field('process_strap')?></em></p>
                </div>
            </div>
        </div>
    </div>
</section>