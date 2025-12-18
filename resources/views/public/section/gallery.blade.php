@if(!empty($data->image->guid))
<section class="text-light relative" data-bgimage="url('{{ $data->image->guid ?? null }}') top">
    <div class="container relative z-2">
        <div class="row g-4">
            <div class="col-lg-12 text-center">
                <div class="spacer-double"></div>
                <h1 class="mb-0">{{ $page->title ?? $data->title }}</h1>

                <div class="spacer-double"></div>
            </div>
        </div>
    </div>
    <div class="sw-overlay op-8"></div>
    <div class="gradient-edge-bottom"></div>
</section>
@endif

<section>
    <div class="container">

        @php
        $categories = [];
        @endphp
        @foreach ($data->item as $item)
            @php
                $itemCategories = $item->title ?? ['general'];
                if (!is_array($itemCategories)) {
                    $itemCategories = [$itemCategories];
                }
            @endphp
            @foreach ($itemCategories as $title)

            @php
                $categories[$title] = $title;
            @endphp

            @endforeach
        @endforeach

        <!-- Gallery Filter Tabs -->
        <div class="text-center mb-5">
            <div class="gallery-filters">
                 <button type="button" class="gallery-filter-btn active" data-filter="all" onclick="testFilter('all')">
                    All Photos
                </button>

                @foreach ($categories as $category)

                <button type="button" class="gallery-filter-btn" data-filter="{{ $category }}" onclick="testFilter('{{ $category }}')">
                    {{ ucfirst($category) }}
                </button>

                @endforeach
            </div>
        </div>

        <div class="gallery-grid" id="bootstrap-image-gallery">

             @foreach ($data->item as $index => $item)

            @php
                $itemCategories = $item->title ?? ['general'];
                if (!is_array($itemCategories)) {
                    $itemCategories = [$itemCategories];
                }
                $category = $itemCategories[0] ?? 'general';
            @endphp

            <div class="gallery-item" data-category="{{ $category }}">
                <a class="lg-item" href="{{ $item->image->guid ?? '' }}" onclick="openCustomLightbox({{ $index }}, event)" data-title="Gallery Image {{ $index + 1 }}">
                    <img src="{{ $item->image->guid ?? '' }}" alt="Gallery Image {{ $index + 1 }}" />
                    <div class="category-badge">{{ ucfirst($category) }}</div>
                    <input type="hidden" name="description" value="{{ $item->description }}">
                </a>
            </div>

            @endforeach
        </div>

    </div>

    <!-- Custom Lightbox HTML -->
    <div id="customLightbox" class="custom-lightbox">
        <div class="custom-lightbox-content">
            <span class="custom-lightbox-close" onclick="closeCustomLightbox()">&times;</span>
            <img id="customLightboxImage" class="custom-lightbox-image" src="" alt="" />
            <div id="customLightboxDescription" class="custom-lightbox-description"></div>
            <div class="custom-lightbox-nav">
                <button onclick="previousImage()">&larr;</button>
                <button onclick="nextImage()">&rarr;</button>
            </div>
        </div>
    </div>

    <!-- Custom Lightbox CSS -->
    <style>
        /* Custom Lightbox Styles */
        .custom-lightbox {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }

        .custom-lightbox.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-lightbox-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }

        .custom-lightbox-image {
            width: 100%;
            height: auto;
            max-width: 90vw;
            max-height: 90vh;
        }

        .custom-lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .custom-lightbox-close:hover {
            opacity: 0.7;
        }

        .custom-lightbox-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .custom-lightbox-nav button {
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            padding: 10px;
        }

        .custom-lightbox-nav button:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
        }

        .custom-lightbox-description {
            position: absolute;
            bottom: 0;
            width: 100%;
            color: white;
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.7);
            margin-top: 15px;
            border-radius: 8px;
            font-size: 16px;
            line-height: 1.5;
            max-width: 80vw;
            margin-left: auto;
            margin-right: auto;
        }

        .custom-lightbox-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .custom-lightbox-image {
            margin-bottom: 0;
        }
    </style>

    <!-- Simple CSS for gallery styling -->
    <style>
        .lightbox-overlay {
            background-color: rgba(0, 0, 0, 0.8) !important;
        }

        .lightbox-image {
            max-width: 90vw !important;
            max-height: 90vh !important;
        }
    </style>

    <script>
        // Custom Lightbox Functions
        let currentImageIndex = 0;
        let galleryImages = [];

        function openCustomLightbox(index, event) {
            event.preventDefault();

            // Get all gallery images and their descriptions
            const imageElements = document.querySelectorAll('.lg-item');
            galleryImages = Array.from(imageElements).map(a => ({
                href: a.href,
                title: a.getAttribute('data-title') || 'Gallery Image',
                description: a.querySelector('input[name="description"]')?.value || ''
            }));

            currentImageIndex = index;
            showImage(currentImageIndex);

            document.getElementById('customLightbox').classList.add('active');
        }

        function closeCustomLightbox() {
            document.getElementById('customLightbox').classList.remove('active');
        }

        function showImage(index) {
            if (galleryImages.length === 0) return;

            // Ensure index is within bounds
            if (index < 0) index = galleryImages.length - 1;
            if (index >= galleryImages.length) index = 0;

            currentImageIndex = index;

            const currentImageData = galleryImages[currentImageIndex];
            const imageElement = document.getElementById('customLightboxImage');
            const descriptionElement = document.getElementById('customLightboxDescription');

            // Set image source and alt text
            imageElement.src = currentImageData.href;
            imageElement.alt = currentImageData.title;

            // Set description text
            descriptionElement.textContent = currentImageData.description;
            descriptionElement.style.display = currentImageData.description ? 'block' : 'none';
        }

        function nextImage() {
            showImage(currentImageIndex + 1);
        }

        function previousImage() {
            showImage(currentImageIndex - 1);
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const lightbox = document.getElementById('customLightbox');
            if (lightbox.classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeCustomLightbox();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                } else if (e.key === 'ArrowLeft') {
                    previousImage();
                }
            }
        });

        // Click outside to close
        document.getElementById('customLightbox').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCustomLightbox();
            }
        });

        // Simple test function
        function testFilter(filter) {
            const filterButtons = document.querySelectorAll('.gallery-filter-btn');
            const galleryItems = document.querySelectorAll('.gallery-item');

            // Remove active class from all buttons
            filterButtons.forEach(function(btn) {
                btn.classList.remove('active');
            });

            // Add active class to clicked button
            event.target.classList.add('active');

            // Filter gallery items
            galleryItems.forEach(function(item) {
                const category = item.getAttribute('data-category');

                if (filter === 'all' || category === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Run when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            const galleryItems = document.querySelectorAll('.gallery-item');

            // Show all items initially
            galleryItems.forEach(function(item) {
                item.style.display = 'block';
            });

            // Lightbox2 will auto-detect images with data-lightbox attribute
            // No manual initialization needed to avoid conflicts

            // Handle image load errors
            document.querySelectorAll('.lg-item img').forEach(function(img) {
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
