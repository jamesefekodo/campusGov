<div class="outerFrame">
    {{-- <img src="{{ associationLogo() }}" alt="{{ env('APP_NAME') }}" class="" width="100px" style="margin: 1rem"> --}}
    @php
        $logoUrl = associationLogo();
        $finalLogoUrl = $logoUrl;

        try {
            $parsedPath = parse_url($logoUrl, PHP_URL_PATH);
            $absolutePath = public_path($parsedPath);

            if ($absolutePath && file_exists($absolutePath)) {
                $info = @getimagesize($absolutePath);
                $mime = $info['mime'] ?? '';

                // Only process PNG/WebP; leave others as-is
                if (in_array($mime, ['image/png', 'image/webp'])) {
                    $filename = 'flattened-' . md5($logoUrl) . '.png';
                    $relativePath = 'storage/flattened/' . $filename;
                    $absoluteDestination = public_path($relativePath);

                    if (!file_exists($absoluteDestination)) {
                        $flattenedDir = public_path('storage/flattened');
                        if (!file_exists($flattenedDir)) {
                            @mkdir($flattenedDir, 0755, true);
                        }

                        $image = match ($mime) {
                            'image/png' => @imagecreatefrompng($absolutePath),
                            'image/webp' => @imagecreatefromwebp($absolutePath),
                            default => null,
                        };

                        if ($image) {
                            $width = imagesx($image);
                            $height = imagesy($image);

                            // Create true color canvas WITH alpha support
                            $output = imagecreatetruecolor($width, $height);
                            imagealphablending($output, false);
                            imagesavealpha($output, true);

                            // Fill with fully transparent color
                            $transparent = imagecolorallocatealpha($output, 0, 0, 0, 127);
                            imagefilledrectangle($output, 0, 0, $width, $height, $transparent);

                            // Copy original image onto transparent canvas
                            imagecopy($output, $image, 0, 0, 0, 0, $width, $height);

                            // Save as PNG to preserve transparency
                            imagepng($output, $absoluteDestination, 6);

                            imagedestroy($image);
                            imagedestroy($output);
                        }
                    }

                    if (file_exists($absoluteDestination)) {
                        $finalLogoUrl = asset($relativePath);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully
        }
    @endphp

    <img src="{{ $finalLogoUrl }}" alt="{{ env('APP_NAME') }}" class="" width="100px"
        style="margin: 1rem; background: transparent;">


    <div class="mailContent">
        @yield('content')
        <br>


        <p style="margin: 0">
            Best regards,
        </p>
        <p style="margin: 0">
            {{ basicSystemInfo()->association_acronym ?? config('app.name') }}.
        </p>
    </div>

</div>



<style>
    :root {
        --system-primary: {{ basicSystemInfo()['primary_colour'] ?: '#3b5edd' }};
        --system-secondary: {{ basicSystemInfo()['secondary_colour'] ?: '#7a97ff' }};
        --system-tertiary: {{ basicSystemInfo()['tertiary_colour'] ?: 'var(--system-secondary)' }};
    }

    .outerFrame {
        width: 100%;
        background: #f0f0f0;
        text-align: center;
        padding: 1rem;
    }

    .mailContent {
        width: 80%;
        background: #ffffff;
        text-align: left;
        margin: 0.2rem auto;
        padding: 1.7rem;
        font-family: Arial, sans-serif;
    }

    .mailLink {
        text-align: center;
    }

    .link-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--system-primary);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .link-button:hover {
        background-color: #77c3ec;
    }

    .otp {
        text-align: center;
        justify-content: center;
    }

    .link-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--system-primary);
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }


    .otp-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--system-primary);
        color: #fff;
        font-size: 1.5rem;
        text-decoration: none;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .otp-button:hover {
        background-color: var(--system-secondary);
    }
</style>
