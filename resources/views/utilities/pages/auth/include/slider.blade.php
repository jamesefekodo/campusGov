<div class="slider">
    <div class="slides">
        @if (homepageSliders()->isNotEmpty())
            @foreach (homepageSliders() as $key => $slider)
                <div class="slide {{ $key === 0 ? 'active' : '' }}">
                    <div class="overlay"></div>

                    <img src="{{ asset('assets/tenant_' . tenant()->id . '/uploads/sliders/' . $slider->background_image) }}"
                        alt="{{ $slider->header }}" />

                    <div class="caption">
                        <p class="caption-header">{{ $slider->header }}</p>
                        <p class="caption-description">{{ $slider->description }}</p>
                    </div>
                </div>
            @endforeach
        @endif
    </div>


    {{-- Navigation Arrows and Dots --}}
    <div class="nav-arrows">
        <span class="arrow prev">&#10094;</span>
        <span class="arrow next">&#10095;</span>
    </div>


    <div class="nav-dots">
        @if (homepageSliders()->isNotEmpty())
            @foreach (homepageSliders() as $key => $slider)
                <span class="dot {{ $key === 0 ? 'active' : '' }}"></span>
            @endforeach
        @endif
    </div>
</div>
