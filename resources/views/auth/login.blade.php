<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $companyInfo?->company_name ?? 'Asha Enterprises' }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef2f4;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        .public-page {
            position: relative;
            width: min(1180px, calc(100% - 30px));
            margin: 20px auto;
            background: #ffffff;
            min-height: calc(100vh - 40px);
            overflow: hidden;
            border: 1px solid #d7dde2;
            box-shadow: 0 12px 35px rgba(0,0,0,.08);
        }

        .public-page::before {
            content: "";
            position: absolute;
            top: 0;
            right: -55px;
            width: 165px;
            height: 100%;
            background: #98c93c;
            transform: skewX(-7deg);
            opacity: .95;
            z-index: 0;
        }

        .public-page::after {
            content: "";
            position: absolute;
            top: 0;
            right: 45px;
            width: 65px;
            height: 100%;
            background: rgba(74, 122, 44, .65);
            transform: skewX(7deg);
            z-index: 0;
        }

        .page-inner {
            position: relative;
            z-index: 2;
            padding: 22px 42px 28px 42px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
            border-radius: 10px;
            background: #fff;
        }

        .brand-name {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.1;
        }

        .brand-subtitle {
            margin-top: 4px;
            color: #13773b;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .share-btn {
            border: 1px solid #d4d8dd;
            background: white;
            border-radius: 7px;
            padding: 8px 16px;
            cursor: pointer;
            font-weight: 700;
        }

        .main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 310px;
            gap: 24px;
            align-items: start;
        }

        .hero-image {
            width: 100%;
            max-height: 230px;
            object-fit: cover;
            display: block;
            border: 1px solid #d6d6d6;
        }

        .section-title {
            margin: 16px 0 8px;
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .section-title.red {
            color: #d92518;
            text-decoration: underline;
        }

        .description {
            white-space: pre-line;
            font-size: 15px;
            line-height: 1.5;
        }

        .side-card {
            margin-bottom: 18px;
            background: rgba(255,255,255,.96);
        }

        .company-photo {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border: 1px solid #d6d6d6;
        }

        .side-heading {
            text-align: center;
            font-size: 15px;
            font-weight: 800;
            margin: 8px 0;
        }

        .staff-box {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .staff-photo {
            width: 105px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #ddd;
            background: #f4f4f4;
        }

        .staff-details {
            flex: 1;
            font-size: 12px;
            line-height: 1.5;
            word-break: break-word;
        }

        .staff-title {
            font-weight: 800;
            margin-bottom: 5px;
        }

        .staff-details a,
        .contact-line a {
            color: #6d9e19;
            text-decoration: none;
        }

        .login-card {
            border: 1px solid #c8d5df;
            background: #d7efff;
            padding: 14px;
        }

        .login-card h3 {
            margin: 0 0 10px;
            text-align: center;
            font-size: 14px;
        }

        .login-input {
            width: 100%;
            height: 34px;
            margin-bottom: 8px;
            border: 1px solid #98a9b5;
            border-radius: 5px;
            padding: 0 10px;
            background: #fff;
        }

        .login-btn {
            width: 100%;
            height: 34px;
            border: 0;
            border-radius: 5px;
            background: #147696;
            color: white;
            font-weight: 800;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #0c5d79;
        }

        .login-error {
            font-size: 11px;
            color: #b91c1c;
            margin: -3px 0 7px;
        }

        .contact-area {
            margin-top: 18px;
            border-top: 1px solid #dde2e5;
            padding-top: 12px;
        }

        .contact-line {
            font-size: 13px;
            margin: 4px 0;
        }

        .map-box {
            border: 1px solid #333;
            background: #fff;
            padding: 5px;
        }

        .map-box iframe {
            display: block;
            width: 100%;
            height: 220px;
            border: 0;
        }

       .links-box {
    margin-top: 18px;
    border: 1px solid #1f2937;
    padding: 14px 18px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,.95);
}

     .company-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 16px;
    border: 1px solid #86b82d;
    border-radius: 6px;
    background: #98c93c;
    color: #ffffff;
    text-decoration: none;
    font-size: 13px;
    font-weight: 700;
    line-height: 1;
    box-shadow: 0 2px 4px rgba(0, 0, 0, .10);
    transition: all .2s ease;
}
.share-wrap {
    position: relative;
}

.share-menu {
    display: none;
    position: absolute;
    top: 44px;
    right: 0;
    min-width: 150px;
    padding: 8px;
    border: 1px solid #d4d8dd;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 10px 25px rgba(0,0,0,.15);
    z-index: 50;
}

.share-menu.show {
    display: block;
}

.share-menu button {
    display: block;
    width: 100%;
    padding: 8px 10px;
    border: 0;
    border-radius: 5px;
    background: transparent;
    text-align: left;
    cursor: pointer;
    font-size: 13px;
}

.share-menu button:hover {
    background: #f1f5f9;
}

.company-link:hover {
    background: #76a525;
    border-color: #76a525;
    color: #ffffff;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, .14);
}
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }

        @media (max-width: 850px) {
            .public-page {
                width: 100%;
                margin: 0;
                border: 0;
            }

            .page-inner {
                padding: 16px;
            }

            .public-page::before,
            .public-page::after {
                display: none;
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                align-items: flex-start;
            }

            .brand-name {
                font-size: 19px;
            }

            .brand-logo {
                width: 48px;
                height: 48px;
            }

            .right-side {
                display: grid;
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .company-photo {
                height: auto;
                max-height: 260px;
            }

            .staff-box {
                justify-content: flex-start;
            }

            .map-box iframe {
                height: 280px;
            }
        }
    </style>
</head>

<body>

<div class="public-page">
    <div class="page-inner">

        <header class="topbar">
            <div class="brand">

                @if($companyInfo?->logo)
                    <img
                        class="brand-logo"
                        src="{{ asset('storage/' . $companyInfo->logo) }}"
                        alt="{{ $companyInfo->company_name }}"
                    >
                @endif

                <div>
                    <div class="brand-name">
                        {{ $companyInfo?->company_name ?? 'Asha Enterprises' }}
                    </div>

                    <div class="brand-subtitle">
                        Branchless Banking & Digital Services
                    </div>
                </div>
            </div>

           <div class="share-wrap">
    <button type="button" class="share-btn" onclick="toggleShareMenu()">
        Share
    </button>

    <div id="shareMenu" class="share-menu">
        <button type="button" onclick="shareFacebook()">Facebook</button>
        <button type="button" onclick="shareWhatsApp()">WhatsApp</button>
        <button type="button" onclick="shareInstagram()">Instagram</button>
        <button type="button" onclick="copyPageLink()">Copy Link</button>
        <button type="button" onclick="nativeShare()">More</button>
    </div>
</div>
        </header>


        <div class="main-grid">

            {{-- LEFT CONTENT --}}
            <main>

                @if($companyInfo?->thumbnail_image)
                    <img
                        class="hero-image"
                        src="{{ asset('storage/' . $companyInfo->thumbnail_image) }}"
                        alt="{{ $companyInfo->company_name }}"
                    >
                @endif


                @if($companyInfo?->description_1)
                    <h2 class="section-title">
                        ❖ Branchless Banking Services
                    </h2>

                    <div class="description">
                        {{ $companyInfo->description_1 }}
                    </div>
                @endif


                @if($companyInfo?->description_2)
                    <h2 class="section-title red">
                        ❖ Our Services
                    </h2>

                    <div class="description">
                        {{ $companyInfo->description_2 }}
                    </div>
                @endif


                <div class="contact-area">

                    @if($companyInfo?->mobile_number)
                        <div class="contact-line">
                            <strong>Mobile:</strong>
                            <a href="tel:{{ $companyInfo->mobile_number }}">
                                {{ $companyInfo->mobile_number }}
                            </a>
                        </div>
                    @endif

                    @if($companyInfo?->phone_number)
                        <div class="contact-line">
                            <strong>Phone:</strong>
                            <a href="tel:{{ $companyInfo->phone_number }}">
                                {{ $companyInfo->phone_number }}
                            </a>
                        </div>
                    @endif

                    @if($companyInfo?->email)
                        <div class="contact-line">
                            <strong>Email:</strong>
                            <a href="mailto:{{ $companyInfo->email }}">
                                {{ $companyInfo->email }}
                            </a>
                        </div>
                    @endif

                    @if($companyInfo?->website)
                        <div class="contact-line">
                            <strong>Website:</strong>
                            <a href="{{ $companyInfo->website }}" target="_blank" rel="noopener noreferrer">
                                {{ $companyInfo->website }}
                            </a>
                        </div>
                    @endif

                </div>

            </main>


            {{-- RIGHT SIDE --}}
            <aside class="right-side">

                @if($companyInfo?->image)
                    <div class="side-card">
                        <img
                            class="company-photo"
                            src="{{ asset('storage/' . $companyInfo->image) }}"
                            alt="{{ $companyInfo->company_name }}"
                        >
                    </div>
                @endif


                <div class="side-card">
                    <div class="side-heading">
                        Contact Person
                    </div>

                    <div class="staff-box">

                        @if($companyInfo?->staff_photo)
                            <img
                                class="staff-photo"
                                src="{{ asset('storage/' . $companyInfo->staff_photo) }}"
                                alt="Contact Person"
                            >
                        @endif

                        <div class="staff-details">

                            @if($companyInfo?->staff_title)
                                <div class="staff-title">
                                    {{ $companyInfo->staff_title }}
                                </div>
                            @endif

                            @if($companyInfo?->staff_mobile_number)
                                <div>
                                    <strong>Mobile:</strong><br>
                                    <a href="tel:{{ $companyInfo->staff_mobile_number }}">
                                        {{ $companyInfo->staff_mobile_number }}
                                    </a>
                                </div>
                            @endif

                            @if($companyInfo?->staff_email)
                                <div style="margin-top:5px;">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:{{ $companyInfo->staff_email }}">
                                        {{ $companyInfo->staff_email }}
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>


                {{-- LOGIN --}}
                <div class="login-card">
                    <h3>Staff Login</h3>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <input
                            class="login-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            required
                            autocomplete="username"
                        >

                        @error('email')
                            <div class="login-error">{{ $message }}</div>
                        @enderror

                        <input
                            class="login-input"
                            type="password"
                            name="password"
                            placeholder="Password"
                            required
                            autocomplete="current-password"
                        >

                        @error('password')
                            <div class="login-error">{{ $message }}</div>
                        @enderror

                        <button class="login-btn" type="submit">
                            Login
                        </button>
                    </form>
                </div>


                {{-- MAP --}}
                <div class="map-box">
                    <iframe
                        src="https://www.google.com/maps?q=28.559276,82.666873&z=14&output=embed"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

            </aside>

        </div>


        {{-- COMPANY LINKS --}}
        @if(isset($companyLinks) && $companyLinks->isNotEmpty())
            <div class="links-box">

                @foreach($companyLinks as $link)
                    <a
                        class="company-link"
                        href="{{ $link->url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {{ $link->title }}
                    </a>
                @endforeach

            </div>
        @endif


        <div class="footer">
            © {{ date('Y') }}
            {{ $companyInfo?->company_name ?? 'Asha Enterprises' }}
        </div>

    </div>
</div>


<script>
    const pageUrl = window.location.href;
    const pageTitle = @json($companyInfo?->company_name ?? 'Asha Enterprises');

    function toggleShareMenu() {
        document.getElementById('shareMenu').classList.toggle('show');
    }

    function shareFacebook() {
        const url = 'https://www.facebook.com/sharer/sharer.php?u=' +
            encodeURIComponent(pageUrl);

        window.open(url, '_blank', 'width=700,height=600');
    }

    function shareWhatsApp() {
        const text = pageTitle + ' - ' + pageUrl;

        window.open(
            'https://wa.me/?text=' + encodeURIComponent(text),
            '_blank'
        );
    }

    async function shareInstagram() {
        try {
            await navigator.clipboard.writeText(pageUrl);
            alert('Link copied. You can now paste it in Instagram.');
            window.open('https://www.instagram.com/', '_blank');
        } catch (error) {
            alert(pageUrl);
        }
    }

    async function copyPageLink() {
        try {
            await navigator.clipboard.writeText(pageUrl);
            alert('Page link copied.');
        } catch (error) {
            alert(pageUrl);
        }
    }

    async function nativeShare() {
        if (navigator.share) {
            try {
                await navigator.share({
                    title: pageTitle,
                    url: pageUrl
                });
            } catch (error) {
                // User cancelled share.
            }
        } else {
            copyPageLink();
        }
    }

    document.addEventListener('click', function (event) {
        const wrap = document.querySelector('.share-wrap');

        if (wrap && !wrap.contains(event.target)) {
            document.getElementById('shareMenu')?.classList.remove('show');
        }
    });
</script>

</body>
</html>