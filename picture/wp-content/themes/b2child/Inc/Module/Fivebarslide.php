<div id="home-row-pchd01" class="<?php if (lmy_get_option('tag_c')) { echo 'mobile-hidden';}?> home_row home_row_0  module-sliders  ">
    <div class="wrapper">
        <div class="home-row-left content-area ">
            <div class="slider-5 slider-5-pchd01  slider-show-title" style="width:1300px;max-width:100%">
                <style>
                    .slider-5-pchd01 .slider-5-bottom .slider-in:nth-child(1),
                    .slider-5-pchd01 .slider-5-bottom .slider-in:nth-child(2) {
                        margin-bottom: 16px;
                    }

                    .slider-5-pchd01 .slider-5-bottom .slider-height {
                        margin-right: 16px;
                    }

                    .slider-5-pchd01 .slider-5-bottom {
                        margin-right: -16px;
                    }
                </style>
                <div class="slider-in-out" style="width:40%;margin-right:16px;">
                    <div class="slider-in-out-row" style="height:100%;width:100%;">
                        <div class="slider-in carousel b2-radius box"
                            data-flickity='{"wrapAround":true,"fullscreen":true,"autoPlay":4000,"imagesLoaded":true,"prevNextButtons":false,"pageDots":true}'
                            style="width:100%">

                            
                            <?php 
			    	            if (!empty(lmy_get_option('Fivebarslide_add'))) {
                                foreach (lmy_get_option('Fivebarslide_add') as $group_item) {
                                        echo '<div class="slider-5-carousel slider-height" style="max-width:100%;height:100%">
                                <div class="slider-info b2-radius">
                                    <a class="link-block" href="' . $group_item['ad_url'] . '"></a>
                                    <picture class="picture">
                                        <source type="image/webp" srcset="' . $group_item['ad_img'] . '" /><img
                                            class="slider-img b2-radius lazy" data-src="' . $group_item['ad_img'] . '"
                                            alt="" src="' . $group_item['ad_img'] . '" />
                                    </picture>
                                    <div class="slider-info-box">

                                        <h2></h2>

                                    </div>
                                </div>
                            </div>';
                                }
                    }?>
                        </div>
                    </div>
                </div>
                <div class="slider-5-bottom" style="width:60%">
                     <?php 
			    	            if (!empty(lmy_get_option('Fivebarslide_addx'))) {
                                foreach (lmy_get_option('Fivebarslide_addx') as $group_item) {
                                        echo '<div class="slider-in b2-radius">
                        <div class="slider-5-right-item slider-height" style="height:0;padding-top:44.871795%">
                            <div class="slider-info b2-radius box">
                                <a class="link-block" href="' . $group_item['ad_url'] . '"></a>
                                <picture class="picture">
                                    <source type="image/webp" srcset="' . $group_item['ad_img'] . '" /><img
                                        class="slider-img b2-radius lazy" data-src="' . $group_item['ad_img'] . '"
                                        alt="" src="' . $group_item['ad_img'] . '" />
                                </picture>
                                <div class="slider-info-box">

                                    <h2></h2>

                                </div>
                            </div>
                        </div>
                    </div>';
                                }
                    }?>
                </div>
            </div>
        </div>
    </div>
</div>