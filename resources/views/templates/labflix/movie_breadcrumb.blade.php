<section class="inner-hero bg_img dark--overlay" data-background="{{ getImage(getFilePath('item_landscape').'/'.$videoItem->image->landscape) }}">
      <div class="container">
        <div class="row">
          <div class="col-lg-7">
            <h2 class="movie-name">{{ __($videoItem->title) }}</h2>
            <ul class="movie-card__meta justify-content-start mt-2 mb-4 style--two">
              <li><i class="far fa-eye color--primary"></i> <span>{{ getAmount($videoItem->view) }}</span></li>
              <li><i class="fas fa-star color--glod"></i> <span>({{ __($videoItem->ratings) }})</span></li>
            </ul>
            <p class="text-white">{{ __($videoItem->preview_text) }}</p>
          </div>
        </div>
      </div>
    </section>
