@php
    $breadcrumb = getContent('breadcrumb.content',true);
@endphp

<section class="inner-banner-section banner-section bg-overlay-black bg_img" data-background="{{ getImage('assets/images/frontend/breadcrumb/'.@$breadcrumb->data_values->background_image, '1778x755') }}">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-xl-12 text-center">
                <div class="banner-content">
                    <h1 class="title text-white">@lang($pageTitle)</h1>
                    <div class="breadcrumb-area">
                        <nav aria-label="breadcrumb" style="{{ app()->getLocale() === 'ar' ?'direction:rtl;':'direction:ltr;' }}>

                        @endauth">
                            <ol class="breadcrumb justify-content-center" style="display: flex; list-style: none; padding: 0; margin: 0; {{ app()->getLocale() === 'ar' ? 'flex-direction: row-reverse; text-align: right;' : 'flex-direction: row; text-align: left;' }}">
                                <li class="breadcrumb-item item" style="{{ app()->getLocale() === 'ar' ? 'padding-left: 0; padding-right: 0.5rem;' : 'padding-right: 0; padding-left: 0.5rem;' }}">
                                    <a href="{{ route('home') }}">@lang('Home')</a>
                                </li>
                                <li class="breadcrumb-item active item" aria-current="page" style="color: #6c757d;">
                                    @lang($pageTitle)
                                </li>
                            </ol>
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
