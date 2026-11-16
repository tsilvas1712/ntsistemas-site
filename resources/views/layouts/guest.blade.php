{{-- Layout das páginas públicas (login). --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
