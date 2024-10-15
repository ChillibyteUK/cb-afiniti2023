<section class="dt_diagram py-5">
    <div class="container-xl">
        <div class="row g-2">
            <div class="col-lg-5 my-auto text-center">
                <?=wp_get_attachment_image(get_field('diagram'),'full',null)?>
            </div>
            <div class="col-lg-7 d-flex flex-column justify-content-around gap-2">
                <div class="bg--grey-100 p-2 dt_diagram__card">
                    <?=wp_get_attachment_image(get_field('strategy_icon'),'large',false,array('class' => 'dt_diagram__icon'))?>
                    <div class="dt_diagram__inner">
                        <h3 class="text--green">Digital Strategy</h3>
                        <p class="mb-2"><?=get_field('strategy_description')?></p>
                        <p class="mb-0"><em><?=get_field('strategy_strap')?></em></p>
                    </div>
                </div>
                <div class="bg--grey-100 p-2 dt_diagram__card">
                    <?=wp_get_attachment_image(get_field('data_icon'),'large',false,array('class' => 'dt_diagram__icon'))?>
                    <div class="dt_diagram__inner">
                        <h3 class="text--dk-purple">Data</h3>
                        <p class="mb-2"><?=get_field('data_description')?></p>
                        <p class="mb-0"><em><?=get_field('data_strap')?></em></p>
                    </div>
                </div>
                <div class="bg--grey-100 p-2 dt_diagram__card">
                    <?=wp_get_attachment_image(get_field('people_icon'),'large',false,array('class' => 'dt_diagram__icon'))?>
                    <div class="dt_diagram__inner">
                        <h3 class="text--purple">People</h3>
                        <p class="mb-2"><?=get_field('people_description')?></p>
                        <p class="mb-0"><em><?=get_field('people_strap')?></em></p>
                    </div>
                </div>
                <div class="bg--grey-100 p-2 dt_diagram__card">
                    <?=wp_get_attachment_image(get_field('process_icon'),'large',false,array('class' => 'dt_diagram__icon'))?>
                    <div class="dt_diagram__inner">
                        <h3 class="text--blue">Process</h3>
                        <p class="mb-2"><?=get_field('process_description')?></p>
                        <p class="mb-0"><em><?=get_field('process_strap')?></em></p>
                    </div>
                </div>
                <div class="bg--grey-100 p-2 dt_diagram__card">
                    <?=wp_get_attachment_image(get_field('system_icon'),'large',false,array('class' => 'dt_diagram__icon'))?>
                    <div class="dt_diagram__inner">
                        <h3 class="text--orange">Systems</h3>
                        <p class="mb-2"><?=get_field('system_description')?></p>
                        <p class="mb-0"><em><?=get_field('system_strap')?></em></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>