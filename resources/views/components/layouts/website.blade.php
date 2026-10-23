<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Five Fingers - Luxury Gem & Jewellery' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/five-fingers-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --ff-gold: #d4af37;
            --ff-gold-light: #f7e7b4;
            --ff-gold-dark: #aa820a;
            --ff-black: #0b0c10;
            --ff-dark: #12141a;
            --ff-dark-card: #191c24;
            --ff-border: rgba(212, 175, 55, 0.2);
            --ff-text: #e6e6e6;
            --ff-text-muted: #9ba1ad;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--ff-black);
            color: var(--ff-text);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .brand-font {
            font-family: 'Cinzel', serif;
            letter-spacing: 0.04em;
        }

        .gold-gradient-text {
            background: linear-gradient(135deg, #fff2be 0%, #d4af37 50%, #997819 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gold {
            background: linear-gradient(135deg, #d4af37 0%, #aa820a 100%);
            color: #0b0c10;
            font-weight: 600;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, #f7e7b4 0%, #d4af37 100%);
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        }

        .btn-outline-gold {
            border: 1px solid var(--ff-gold);
            color: var(--ff-gold);
            background: transparent;
            padding: 9px 22px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-gold:hover {
            background: var(--ff-gold);
            color: #0b0c10;
            box-shadow: 0 6px 18px rgba(212, 175, 55, 0.25);
        }

        .luxury-card {
            background: var(--ff-dark-card);
            border: 1px solid var(--ff-border);
            border-radius: 14px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .luxury-card:hover {
            border-color: rgba(212, 175, 55, 0.55);
            transform: translateY(-4px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.45);
        }
    </style>

    @livewireStyles
</head>
<body>

    @include('livewire.website.partials.navbar')

    <main>
        {{ $slot }}
    </main>

    @include('livewire.website.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
