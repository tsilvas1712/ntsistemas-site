{{--
    Layout das páginas autenticadas (equivalente ao AppShell do React):
    sidebar fixa no desktop, gaveta no celular, header e footer.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <div class="min-h-svh bg-background" x-data="{ mobileOpen: false }">
            <aside class="fixed inset-y-0 left-0 z-40 hidden w-72 lg:block">
                <x-layout.sidebar />
            </aside>

            <x-ui.sheet x-model="mobileOpen" side="left" :show-close-button="false" class="w-72 border-0 bg-sidebar p-0 sm:max-w-72">
                <x-ui.sheet.title class="sr-only">Navegação</x-ui.sheet.title>
                <x-layout.sidebar />
            </x-ui.sheet>

            <div class="flex min-h-svh flex-col lg:pl-72">
                <x-layout.header />
                <main class="flex-1 px-4 py-4 md:px-6 md:py-6">
                    {{ $slot }}
                </main>
                <x-layout.footer />
            </div>
        </div>
    </body>
</html>
