{{-- <div class="slider">
    <div class="slides">
        @if (homepageSliders()->isNotEmpty())
            @foreach (homepageSliders() as $key => $slider)
                <div class="slide {{ $key === 0 ? 'active' : '' }}">
                    <div class="overlay"></div>

                    <img src="{{ asset('assets/uploads/homepageSlider/' . $slider->background_image) }}"
                        alt="{{ $slider->header }}" />

                    <div class="caption">
                        <p class="caption-header">{{ $slider->header }}</p>
                        <p class="caption-description">{{ $slider->description }}</p>
                    </div>
                </div>
            @endforeach

        @endif
    </div>


    Navigation Arrows and Dots
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
</div> --}}

{{-- ######################################################### --}}
<div class="slider">
    <div class="slides">

        <div class="slide active">
            <div class="overlay"></div>
            <img src="https://images.unsplash.com/photo-1497493292307-31c376b6e479?auto=format&fit=crop&w=1600&q=80"
                alt="Campus">
            <div class="caption">
                <p class="caption-header">Welcome to CampusGov</p>
                <p class="caption-description">
                    A modern digital platform for educational institutions.
                </p>
            </div>
        </div>

        <div class="slide">
            <div class="overlay"></div>
            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1600&q=80"
                alt="Students">
            <div class="caption">
                <p class="caption-header">Student Management</p>
                <p class="caption-description">
                    Manage admissions, records and student activities efficiently.
                </p>
            </div>
        </div>

        <div class="slide">
            <div class="overlay"></div>
            <img src="https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=crop&w=1600&q=80"
                alt="Library">
            <div class="caption">
                <p class="caption-header">Digital Learning</p>
                <p class="caption-description">
                    Connect students, lecturers and administrators seamlessly.
                </p>
            </div>
        </div>

        <div class="slide">
            <div class="overlay"></div>
            <img src="https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=1600&q=80"
                alt="Lecture Hall">
            <div class="caption">
                <p class="caption-header">Smart Campus Experience</p>
                <p class="caption-description">
                    Everything your institution needs in one integrated platform.
                </p>
            </div>
        </div>

        <div class="slide">
            <div class="overlay"></div>
            <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1600&q=80"
                alt="University">
            <div class="caption">
                <p class="caption-header">Innovation Meets Education</p>
                <p class="caption-description">
                    Built to simplify administration and improve learning outcomes.
                </p>
            </div>
        </div>

    </div>

    <!-- Navigation Arrows -->
    <div class="nav-arrows">
        <span class="arrow prev">&#10094;</span>
        <span class="arrow next">&#10095;</span>
    </div>

    <!-- Navigation Dots -->
    <div class="nav-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
</div>
