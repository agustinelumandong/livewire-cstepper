<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Livewire CStepper' }}</title>
    
    <!-- Tailwind CSS -->
    <link href="{{ asset('vendor/livewire-cstepper/app.css') }}" rel="stylesheet">
    
    <!-- WireUI Styles -->
    @wireUiStyles
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto py-8">
        {{ $slot }}
    </div>

    <!-- WireUI Scripts -->
    @wireUiScripts
    
    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    @stack('scripts')
</body>
</html>