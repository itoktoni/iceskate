<section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
    <div class="container relative z-2">
        <div class="row g-4">
            <div class="col-lg-12 text-center">
                <div class="spacer-double"></div>
                <h1 class="mb-0">{{ $page->title ?? '' }}</h1>
                <div class="spacer-double"></div>
            </div>
        </div>
    </div>
    <div class="sw-overlay op-8"></div>
    <div class="gradient-edge-bottom"></div>
</section>

<section>
    <div class="container">

        @php
        $categories = [];
        @endphp
        @foreach ($data->item as $item)
            @foreach ($item->title as $title)

            @php
                $categories[$title] = $title;
            @endphp

            @endforeach
        @endforeach

        <!-- Gallery Filter Tabs -->
        <div class="text-center mb-5">
            <div class="gallery-filters">
                 <button type="button" class="gallery-filter-btn active" data-filter="all">
                    All Photos
                </button>

                @foreach ($categories as $category)

                <button type="button" class="gallery-filter-btn" data-filter="{{ $category }}">
                    {{ $category }}
                </button>

                @endforeach
            </div>
        </div>

        <div class="gallery-grid" id="bootstrap-image-gallery">

             @foreach ($data->item as $index => $item)

            @php
                $itemCategories = $item->title ?? ['general'];
            @endphp

            <div class="gallery-item"
                 @foreach($itemCategories as $cat)
                 data-category-{{ $loop->index }}="{{ $cat }}"
                 @endforeach
                 data-categories="{{ implode(',', $itemCategories) }}">
                <a class="lg-item" data-src="{{ $item->image->guid ?? '' }}">
                    <img src="{{ $item->image->guid ?? '' }}" alt="Gallery Image {{ $index + 1 }}" />
                    <div class="category-badge">{{ $itemCategories[0] ?? 'general' }}</div>
                </a>
            </div>

            @endforeach
        </div>

    </div>

    <!-- PhotoSwipe CSS -->
    <link rel="stylesheet" href="https://unpkg.com/photoswipe@5.4.4/dist/photoswipe.css">
    <!-- Simple CSS for PhotoSwipe thumbnails -->
    <style>
        .pswp__thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin: 4px;
            border-radius: 4px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .pswp__thumbnail:hover,
        .pswp__thumbnail--active {
            opacity: 1;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gallery filter functionality
            const filterButtons = document.querySelectorAll('.gallery-filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');

                    // Update active button
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    // Filter gallery items
                    galleryItems.forEach(item => {
                        const categories = item.getAttribute('data-categories');
                        const categoryList = categories ? categories.split(',') : [];

                        if (filter === 'all' || categoryList.includes(filter)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Initialize PhotoSwipe
            const lightbox = new PhotoSwipeLightbox({
                gallery: '#bootstrap-image-gallery',
                children: '.lg-item',
                showHideAnimationType: 'zoom',

                wheelToZoom: true,
                pinchToClose: true,
                close: true,
                arrowPrev: true,
                arrowNext: true,
                zoom: true,

                // Loop images
                loop: true,

                // Spacing between slides
                spacing: 0.1,

                // Background opacity
                bgOpacity: 0.9,

                // Close on escape
                escKey: true,

                // Arrow keys to navigate
                arrowKeys: true,

                // Allow touch move
                allowTouchMove: true,

                // Double click to zoom
                doubleTapAction: 'zoom',

                // Initial zoom level
                initialZoomLevel: 'fit',

                // Secondary zoom level
                secondaryZoomLevel: 2,

                // Max zoom level
                maxZoomLevel: 3,

                // Min zoom level
                minZoomLevel: 'fit',

                // Keyboard controls
                keyboardControl: true,

                // Mouse wheel zoom
                mouseWheel: true,

                // Close when clicked outside
                closeOnBackdropClick: true,

                // Image options
                imageClickAction: 'zoom',
                bgClickAction: 'close',

                // Preload images
                preload: [1, 1],

                // Error message
                errorMsg: 'Failed to load image.',

                // Mobile options
                mobileLayoutBreakpoint: 480,

                // Focus management
                returnFocus: true,

                // A11y options
                ariaLabel: 'Image gallery',

                // Animation options
                showAnimationDuration: 333,
                hideAnimationDuration: 333,

                // Zoom animation duration
                zoomAnimationDuration: 333,

                // Initial slide index
                index: 0,

                // Focus trap
                focus: true,

                // Trap focus within gallery
                trapFocus: true,

                // History API
                history: false,

                // Gallery ID for URL hash
                galleryUID: 'gallery'
            });

            // Initialize the lightbox
            lightbox.init();

            // Handle image load errors
            document.querySelectorAll('.lg-item img').forEach(img => {
                img.addEventListener('error', function() {
                    this.style.display = 'none';
                    const parent = this.closest('.gallery-item');
                    if (parent) {
                        parent.style.display = 'none';
                    }
                });
            });
        });
    </script>

    <style>
        /* Gallery Filters */
        .gallery-filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 2rem;
        }

        .gallery-filter-btn {
            padding: 12px 24px;
            border: 2px solid #007bff;
            background: transparent;
            color: #007bff;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 0;
            min-width: 120px;
        }

        .gallery-filter-btn:hover {
            background: #007bff;
            color: white;
        }

        .gallery-filter-btn.active {
            background: #007bff;
            color: white;
        }

        /* Gallery Grid Layout */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 0;
        }

        .gallery-item {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .gallery-item .lg-item {
            display: block;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.05);
        }

        .category-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 10;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 15px;
            }

            .gallery-filters {
                gap: 6px;
            }

            .gallery-filter-btn {
                padding: 10px 20px;
                font-size: 13px;
                min-width: 100px;
            }
        }

        @media (max-width: 576px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 12px;
            }

            .gallery-filters {
                gap: 4px;
            }

            .gallery-filter-btn {
                padding: 8px 16px;
                font-size: 12px;
                min-width: 80px;
            }
        }
    </style>
</section>
